<?php
namespace App\Controller\Fiche\Fournisseur;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Fournisseur;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Fiche client controller.
 *
 * @Route("fiche/fournisseur/achat/document")
 */
class AchatParDocumentController extends BaseController {

    public function getTitreAchatParDocumentByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Référence document contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('typeDoc')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Type document : ' . $request->query->get('typeDoc');
        }
        $dateFormat = $this->get('app.format_date');
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date création >= ' . str_replace('/', '-', $request->query->get('startDateCreation'));
            }
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date création <= ' . str_replace('/', '-', $request->query->get('endDateCreation'));
            }
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getAchatParDocumentQbByParametres($em, $request, $fournisseur) {
        $qb = $em->getRepository('App\\Entity\\Stock')
                ->createQueryBuilder('a')
                ->select('a.id', 'a.designation', 'a.typeDoc', 'sum(a.ttc) as ttc', 'br.regle as brregle','br.reste as brreste','a.dateCreation')
                ->leftJoin('a.bonReception', 'br');
        $qb->where('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);
        if ($request->query->get('code')) {
            $qb->andWhere('a.designation like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        if ($request->query->get('typeDoc')) {
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $request->query->get('typeDoc'));
        }
        $dateFormat = $this->get('app.format_date');
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
            }
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
            }
        }
        return $qb;
    }

    /**
     * Liste des achats par document
     *
     * @Route("/{id}", name="fiche_fournisseur_achat_document", methods={"GET"})
     */
    public function achatParDocumentAction(Request $request, Fournisseur $fournisseur, $offset = 0, $limit = 10) {
        $em = $this->getEm();
        $qb = $this->getAchatParDocumentQbByParametres($em, $request, $fournisseur->getId());
        if (!$request->get('sort') or $request->get('sort') !== 'a.createdAt') {
            $qb->orderBy('a.id', 'DESC');
        }
        $qb->addGroupBy('a.designation');

        $totalItems = $qb->getQuery()->getResult();
        $totalCountItems = count($totalItems);
        if ($request->query->get('limit') and $request->query->get('limit')!=='') {
            $limit = $request->query->get('limit');
        }
        if ($request->query->get('offset')) {
            $offset = $request->query->get('offset') * $limit;
        }
        $currentPage = ($offset + $limit) / $limit;
        $lastPage = intval($totalCountItems / $limit);

        if ($totalCountItems % $limit != 0) {
            $lastPage++;
        }
        $items = $qb
                ->orderBy($request->query->get('sort')?$request->query->get('sort'):'a.id',$request->query->get('direction')?$request->query->get('direction'):'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        $articles = $em->getRepository('App\\Entity\\Article')->findAll();
        return $this->render('fiche/fournisseur/achatParDocument.html.twig', array(
                    'pagination' => $items,
                    'nombreVentes' => $totalCountItems,
                    'articles' => $articles,
                    'fournisseur' => $fournisseur,
                    'currentPage' => $currentPage,
                    'countCurrentPage' => count($items),
                    'lastPage' => $lastPage
        ));
    }

    /**
     * Export excel vente par document
     *
     * @Route("/export/xls", name="fiche_client_vente_document_xls", methods={"GET"})
     */
    public function achatParDocumentExportXlsAction(Request $request) {
        $em = $this->getEm();
        $qb = $this->getVenteParDocumentQbByParametres($em, $request, $request->query->get('fournisseur'))
                ->addGroupBy('a.designation');
        $fournisseur = $em->getRepository('App\\Entity\\Fournisseur')->find($request->query->get('fournisseur'));
        $titre = $this->getTitreVenteParDocumentByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'venteParDocument' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des achats par document de fournisseur ' . $fournisseur . ' ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Document')
                ->setCellValue('B4', 'Type document')
                ->setCellValue('C4', 'Total TTC')
                ->setCellValue('D4', 'Réglé')
                ->setCellValue('E4', 'Reste')
                ->setCellValue('F4', 'Date création');
        $i = 5;
        for ($j = 0; $j < count($entities); $j++) {
            $vente = $entities[$j];
            $ttc = $vente['typeDoc'] === 'bl' ? $vente['ttc'] : $vente['ttc'] + 0.500;
            $regle = $vente['typeDoc'] == 'bl' ? $vente['blregle'] : $vente['fregle'];
            $reste = $vente['typeDoc'] == 'bl' ? $vente['blreste'] : $vente['freste'];
            $sheet->setCellValue('A' . $i, $entities[$j]['designation'])
                    ->setCellValue('B' . $i, $entities[$j]['typeDoc'])
                    ->setCellValue('C' . $i, number_format($ttc, '3', '.', ''))
                    ->setCellValue('D' . $i, number_format($regle, '3', '.', ''))
                    ->setCellValue('E' . $i, number_format($reste, '3', '.', ''))
                    ->setCellValue('F' . $i, $entities[$j]['dateCreation'] ? $entities[$j]['dateCreation']->format('d-m-Y') : '');
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('venteParDocument');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        //custom config
        $this->get('app.excel_custom_config')->setTitre($phpExcelObject);
        $this->get('app.excel_custom_config')->setHeader($phpExcelObject);
        $this->get('app.excel_custom_config')->setBody($phpExcelObject);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
        );
        $response->headers->set('Content-Type', 'text/xls; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);
        return $response;
    }

}

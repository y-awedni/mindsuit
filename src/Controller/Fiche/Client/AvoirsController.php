<?php
namespace App\Controller\Fiche\Client;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Fiche client controller.
 *
 * @Route("fiche/client/avoirs")
 */
class AvoirsController extends BaseController {
    //__________________________________________________________________________etat des avoirs
    public function getTitreAvoirsByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('codeAvoir')) {
            $chaine .= 'Référence avoir contient ' . $request->query->get('codeAvoir');
            $i++;
        }
        if ($request->query->get('codeArticle')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Référence article contient : ' . $request->query->get('codeArticle');
        }
        if ($request->query->get('designationArticle')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Désignation article contient : ' . $request->query->get('designationArticle');
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

    public function getAvoirsQbByParametres($em, $request, $client) {
        $qb = $em->getRepository('App\\Entity\\LigneFactureAvoir')
                ->createQueryBuilder('a')
                ->select('a.id', 'fav.code as favCode', 'art.code as artCode', 'a.designation', 'a.qte', 'a.ttc', 'a.stock', 'a.reglement', 'fav.dateCreation')
                ->leftJoin('a.article', 'art')
                ->leftJoin('a.factureAvoir', 'fav')
                ->leftJoin('fav.facture', 'fact');
        $qb->where('fact.client = :client')->setParameter('client', $client);
        if ($request->query->get('codeAvoir')) {
            $qb->andWhere('fav.code like :codeAvoir')->setParameter('codeAvoir', '%' . $request->query->get('codeAvoir') . '%');
        }
        if ($request->query->get('codeArticle')) {
            $qb->andWhere('art.code like :codeArticle')->setParameter('codeArticle', '%' . $request->query->get('codeArticle') . '%');
        }
        if ($request->query->get('designationArticle')) {
            $qb->andWhere('a.designation like :designationArticle')->setParameter('designationArticle', '%' . $request->query->get('designationArticle') . '%');
        }
        $dateFormat = $this->get('app.format_date');
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $qb->andWhere('fav.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
            }
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $qb->andWhere('fav.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
            }
        }
        return $qb;
    }

    /**
     * Liste des avoirs
     *
     * @Route("/{id}", name="fiche_client_avoirs", methods={"GET"})
     */
    public function avoirsAction(Request $request, Client $client, $offset = 0, $limit = 10) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAvoirsQbByParametres($em, $request, $client->getId());
        if (!$request->get('sort') or $request->get('sort') !== 'a.createdAt') {
            $qb->orderBy('a.id', 'DESC');
        }
        $totalItems = $qb->getQuery()->getResult();
        $totalCountItems = count($totalItems);
        if ($request->query->get('limit')) {
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
        return $this->render('fiche/client/avoirs.html.twig', array(
                    'pagination' => $items,
                    'total' => $totalCountItems,
                    'client' => $client,
                    'currentPage' => $currentPage,
                    'countCurrentPage' => count($items),
                    'lastPage' => $lastPage
        ));
    }

    /**
     * Export excel vente par document
     *
     * @Route("/export/xls", name="fiche_client_avoirs_xls", methods={"GET"})
     */
    public function avoirsExportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAvoirsQbByParametres($em, $request, $request->query->get('client'))
                ->addGroupBy('a.article');
        $client = $em->getRepository('App\\Entity\\Client')->find($request->query->get('client'));
        $titre = $this->getTitreAvoirsByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'avoirsDeClient' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des avoirs de client ' . $client . ' ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet
                ->setCellValue('A4', 'Référence avoir')
                ->setCellValue('B4', 'Référence article')
                ->setCellValue('C4', 'Désignation article')
                ->setCellValue('D4', 'Quantité vendus')
                ->setCellValue('E4', 'Total TTC')
                ->setCellValue('F4', 'Retourné')
                ->setCellValue('G4', 'Remboursé')
                ->setCellValue('H4', 'Date');
        $i = 5;
        for ($j = 0; $j < count($entities); $j++) {

            $vente = $entities[$j];
            $sheet->setCellValue('A' . $i, $vente['favCode'])
                    ->setCellValue('B' . $i, $vente['artCode'])
                    ->setCellValue('C' . $i, $vente['designation'])
                    ->setCellValue('D' . $i, $vente['qte'])
                    ->setCellValue('E' . $i, number_format($vente['ttc'], '3', '.', ' '))
                    ->setCellValue('F' . $i, $vente['stock'] ? 'oui' : 'non')
                    ->setCellValue('G' . $i, $vente['reglement'] ? 'oui' : 'non')
                    ->setCellValue('H' . $i, $vente['dateCreation']);
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Liste des avoirs');
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



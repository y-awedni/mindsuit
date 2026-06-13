<?php

namespace App\Controller\Fiche\Client;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Client;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Fiche client controller.
 *
 * @Route("fiche/client/reglements")
 */
class ReglementsController extends Controller {

    //__________________________________________________________________________etat des reglements
    public function getTitreReglementsByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('designation')) {
            $chaine .= 'Désignation contient ' . $request->query->get('designation');
            $i++;
        }
        if ($request->query->get('typeDoc')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Type document : ' . $request->query->get('typeDoc');
        }
        if ($request->query->get('modeReglement')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Mode réglement : ' . $request->query->get('modeReglement');
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

    public function getReglementsQbByParametres($em, $request, $client) {
        $qb = $em->getRepository('App\\Entity\\Mouvement')
                ->createQueryBuilder('a')
                ->select('a.id', 'a.designation', 'a.typeDoc', 'a.ttc', 'a.modeReglement', 'a.dateEcheance', 'a.numDoc', 'a.etat', 'a.dateCreation');
        $qb->where('a.client = :client')->setParameter('client', $client);

        if ($request->query->get('designation')) {
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $request->query->get('designation') . '%');
        }
        if ($request->query->get('typeDoc')) {
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $request->query->get('typeDoc'));
        }
        $mvt_modeReglement = null;
        if ($request->query->get('modeReglement') and $request->query->get('modeReglement') !== 'tous') {
            $mvt_modeReglement = $request->query->get('modeReglement');
            $qb->andWhere('a.modeReglement like :modeReglement')->setParameter('modeReglement', $mvt_modeReglement);
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
     * Liste des reglements
     *
     * @Route("/{id}", name="fiche_client_reglements", methods={"GET"})
     */
    public function reglementsAction(Request $request, Client $client, $offset = 0, $limit = 10) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getReglementsQbByParametres($em, $request, $client->getId());
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
        return $this->render('fiche/client/reglements.html.twig', array(
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
     * @Route("/export/xls", name="fiche_client_reglements_xls", methods={"GET"})
     */
    public function reglementsExportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getReglementsQbByParametres($em, $request, $request->query->get('client'));
        $client = $em->getRepository('App\\Entity\\Client')->find($request->query->get('client'));
        $titre = $this->getTitreReglementsByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'reglementsDeClient' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des reglements de client ' . $client . ' ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet
                ->setCellValue('A4', 'Réference document')
                ->setCellValue('B4', 'Type document')
                ->setCellValue('C4', 'Montant TTC')
                ->setCellValue('D4', 'Mode réglement')
                ->setCellValue('E4', 'Date écheance')
                ->setCellValue('F4', 'N°doc')
                ->setCellValue('G4', 'Etat')
                ->setCellValue('H4', 'Date');
        $i = 5;
        for ($j = 0; $j < count($entities); $j++) {
            $vente = $entities[$j];
            $sheet->setCellValue('A' . $i, $vente['designation'])
                    ->setCellValue('B' . $i, $vente['typeDoc'] == 'bl' ? 'Bon de livraison' : 'Facture')
                    ->setCellValue('C' . $i, $vente['ttc'])
                    ->setCellValue('D' . $i, $vente['modeReglement'])
                    ->setCellValue('E' . $i, $vente['dateEcheance'])
                    ->setCellValue('F' . $i, $vente['numDoc'])
                    ->setCellValue('G' . $i, $vente['etat'])
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

<?php

namespace AppBundle\Controller\Fiche\Fournisseur;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Fournisseur;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Fiche Fournisseur controller.
 *
 * @Route("fiche/fournisseur/achat/article")
 */
class AchatParArticleController extends Controller {

    public function getTitreAchatParArticleByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Référence article contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('designation')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Désignation article contient : ' . $request->query->get('designation');
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getAchatParArticleQbByParametres($em, $request, $fournisseur) {
        $qb = $em->getRepository('AppBundle:Stock')
                ->createQueryBuilder('a')
                ->select('a.id', 'art.code as code', 'art.designation as designation', 'sum(a.qte) as qte', 'sum(a.ttc) as ttc')
                ->leftJoin('a.article', 'art');
        $qb->where('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);
        if ($request->query->get('designation')) {
            $qb->andWhere('art.designation like :designation')->setParameter('designation', '%' . $request->query->get('designation') . '%');
        }
        if ($request->query->get('code')) {
            $qb->andWhere('art.code like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        return $qb;
    }

    /**
     * Liste des articles par document
     *
     * @Route("/{id}", name="fiche_fournisseur_achat_article", methods={"GET"})
     */
    public function achatParArticleAction(Request $request, Fournisseur $fournisseur, $offset = 0, $limit = 10) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAchatParArticleQbByParametres($em, $request, $fournisseur->getId());
        if (!$request->get('sort') or $request->get('sort') !== 'a.createdAt') {
            $qb->orderBy('a.id', 'DESC');
        }
        $qb->addGroupBy('a.article');
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
        return $this->render('fiche/fournisseur/achatParArticle.html.twig', array(
                    'pagination' => $items,
                    'nombreAchats' => $totalCountItems,
                    'fournisseur' => $fournisseur,
                    'currentPage' => $currentPage,
                    'countCurrentPage' => count($items),
                    'lastPage' => $lastPage
        ));
    }

    /**
     * Export excel achat par document
     *
     * @Route("/export/xls", name="fiche_fournisseur_achat_article_xls", methods={"GET"})
     */
    public function achatParArticleExportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAchatParArticleQbByParametres($em, $request, $request->query->get('fournisseur'))
                ->addGroupBy('a.article');

        $fournisseur = $em->getRepository('AppBundle:Fournisseur')->find($request->query->get('fournisseur'));
        $titre = $this->getTitreAchatParArticleByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'achatParArticle' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des achats par article de fournisseur ' . $fournisseur . ' ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Référence article')
                ->setCellValue('B4', 'Désignation article')
                ->setCellValue('C4', 'Quantité vendus')
                ->setCellValue('D4', 'Total TTC');
        $i = 5;
        for ($j = 0; $j < count($entities); $j++) {
            $vente = $entities[$j];
            $sheet->setCellValue('A' . $i, $vente['code'])
                    ->setCellValue('B' . $i, $vente['designation'])
                    ->setCellValue('C' . $i, $vente['qte'])
                    ->setCellValue('D' . $i, number_format($vente['ttc'], '3', '.', ' '));
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Liste des ventes par article');
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

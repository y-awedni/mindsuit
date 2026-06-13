<?php

namespace App\Controller;

use App\Entity\FactureAvoir;
use App\Entity\Facture;
use App\Entity\LigneFactureAvoir;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use Numbers_Words;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Factureavoir controller.
 *
 * @Route("factureavoir")
 */
class FactureAvoirController extends BaseController {
    
    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('facture')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $facture = $em->getRepository('App\\Entity\\Facture')->findOneById($request->query->get('facture'));
            $chaine .= 'Code facture : ' . $facture->getCode();
        }
        if ($request->query->get('client')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $client = $em->getRepository('App\\Entity\\Client')->findOneById($request->query->get('client'));
            $chaine .= 'Code client : ' . $client->getCode();
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

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\FactureAvoir')->createQueryBuilder('a');
        $qb->join('a.facture', 'f');
        $qb->join('f.client', 'clt');
        $code = null;
        if ($request->query->get('code')) {
            $code = $request->query->get('code');
            $qb->where('a.code like :code')->setParameter('code', '%' . $code . '%');
        }
        $facture = null;
        if ($request->query->get('facture')) {
            $facture = $request->query->get('facture');
            $qb->andWhere('a.facture = :facture')->setParameter('facture', $facture);
        }
        $client = null;
        if ($request->query->get('client')) {
            $client = $request->query->get('client');
            $qb->andWhere('f.client = :client')->setParameter('client', $client);
        }
        $dateFormat = $this->get('app.format_date');
        $startDateCreation = null;
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
            }
        }
        $endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
            }
        }
        return $qb;
    }

    /**
     * Lists all factureAvoir entities.
     *
     * @Route("/", name="factureavoir_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $factures = $em->getRepository('App\\Entity\\Facture')->findAll();
        $clients = $em->getRepository('App\\Entity\\Client')->findAll();
        $qb = $this->getQbByParametres($em, $request);
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        return $this->render('factureavoir/index.html.twig', array(
                    'pagination' => $pagination,
                    'factures' => $factures,
                    'clients' => $clients
        ));
    }

    /**
     * Lists all stocks entities.
     *
     * @Route("/export/xls", name="factureavoir_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'avoirs' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des avoirs ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Réf')
                ->setCellValue('B4','Facture')
                ->setCellValue('C4','Code client')
                ->setCellValue('D4','Raison social client')
                ->setCellValue('E4', 'Total HT')
                ->setCellValue('F4', 'Total Remise')
                ->setCellValue('G4', 'Total TVA')
                ->setCellValue('H4', 'Total TTC')
                ->setCellValue('I4', 'Terminé')
                ->setCellValue('J4', 'Date création')
                ->setCellValue('K4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A' . $i, $entity->getCode())
                    ->setCellValue('B'.$i,$entity->getFacture())
                    ->setCellValue('C' . $i, $entity->getFacture()->getClient()->getCode())
                    ->setCellValue('D' . $i, $entity->getFacture()->getClient()->getRs())
                    ->setCellValue('E' . $i, $entity->getHt())
                    ->setCellValue('F' . $i, $entity->getRemise())
                    ->setCellValue('G' . $i, $entity->getTva())
                    ->setCellValue('H' . $i, $entity->getTotal())
                    ->setCellValue('I' . $i, $entity->getTermine()?'Terminé':'En cours')
                    ->setCellValue('J' . $i, $entity->getDateCreation())
                    ->setCellValue('K' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Avoirs');
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
    
    
    /**
     * Lists all factureAvoir entities.
     *
     * @Route("/{id}/facture", name="factureavoir_facture", methods={"GET"})
     */
    public function factureAvoirAction(Request $request, Facture $facture) {
        if (!$facture->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut terminer la facture pour faire un avoir');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        if ($facture->getRegle() < $facture->getTotal()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut régler la facture pour faire un avoir');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('App\\Entity\\FactureAvoir')->createQueryBuilder('a');
        $qb->join('a.facture', 'f');
        $qb->where('f.id=' . $facture->getId());
        $query = $qb->getQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        return $this->render('factureavoir/facture/index.html.twig', array(
                    'pagination' => $pagination,
                    'facture' => $facture
        ));
    }

    /**
     * Redirect to a new factureAvoir entity.
     *
     * @Route("/facture/new/id", name="factureavoir_facture_new_id", methods={"GET"})
     */
    public function factureNewIdAction(Request $request) {
        if ($request->query->get('facture')) {
            return $this->redirectToRoute('factureavoir_facture_new', array('id' => $request->query->get('facture')));
        }
        $this->get('session')->getFlashBag()->add('info', 'Il faut sélectionner une facture pour faire un avoir');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Creates a new factureAvoir entity.
     *
     * @Route("/{id}/facture/new", name="factureavoir_facture_new", methods={"GET", "POST"})
     */
    public function factureNewAction(Request $request, Facture $facture, \App\Service\TimbreProvider $timbreProvider) {
        if (!$facture->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut terminer la facture pour faire un avoir');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        if ($facture->getRegle() < $facture->getTotal()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut régler la facture pour faire un avoir');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        $factureAvoir = new FactureAvoir();
        $factureAvoir->setFacture($facture);
        foreach ($facture->getLignesFactures() as $ligne) {
            $ligneFav = new LigneFactureAvoir();
            $ligneFav->setArticle($ligne->getArticle());
            $ligneFav->setDesignation($ligne->getDesignation());
            $ligneFav->setQte(0);
            $ligneFav->setQteMax($ligne->getQte());
            $ligneFav->setPrixUnitaire($ligne->getPrixUnitaire());
            $ligneFav->setRemise($ligne->getRemise());
            $ligneFav->setTva($ligne->getTva());
            $ligneFav->setTtc(0);
            $factureAvoir->addLigneFactureAvoir($ligneFav);
        }
        $form = $this->createForm('App\Form\FactureAvoirType', $factureAvoir);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $maxAvoire = 0;
            foreach ($factureAvoir->getLigneFactureAvoirs() as $key => $ligne) {
                $maxInitiale = $ligne->getQteMax();
                //qte des avoirs de cette article sur cette facture
                $qb = $em->getRepository('App\\Entity\\LigneFactureAvoir')->createQueryBuilder('lfa');
                $qb->select('sum(lfa.qte) as somme')
                        ->join('lfa.factureAvoir', 'fa')
                        ->join('fa.facture', 'f')
                        ->where('f.id=' . $facture->getId())
                        ->andWhere('fa.termine=true')
                        ->andWhere('lfa.article=' . $ligne->getArticle()->getId());
                $query = $qb->getQuery();
                $maxAvoire = $query->getSingleScalarResult();
                // die($ligne->getQte()." - ".$maxInitiale." - ".$maxAvoire);
                if ($ligne->getQte() > $maxInitiale - $maxAvoire) {
                    $valuer = $maxInitiale - $maxAvoire;
                    $msg = $valuer === 0 ? "Quantité dépassé" : "Cette valeur doit être inférieur ou égale à " . $valuer;
                    $form->get('ligneFactureAvoirs')[$key]->get('qte')->addError(new FormError($msg));
                }
            }
            if ($form->isValid()) {
                // Capture the timbre that applied at creation (avoirs now bear it too).
                $factureAvoir->setTimbre($timbreProvider->getValeur());
                $em->persist($factureAvoir);
                $em->flush($factureAvoir);
                return $this->redirectToRoute('factureavoir_show', array('id' => $factureAvoir->getId()));
            }
        }

        return $this->render('factureavoir/facture/new.html.twig', array(
                    'factureAvoir' => $factureAvoir,
                    'form' => $form->createView()
        ));
    }

    /**
     * Finds and displays a factureAvoir entity.
     *
     * @Route("/{id}/show", name="factureavoir_show", methods={"GET"})
     */
    public function showAction(FactureAvoir $factureAvoir) {
        return $this->render('factureavoir/show.html.twig', array(
                    'factureAvoir' => $factureAvoir
        ));
    }

    /**
     * Displays a form to edit an existing factureAvoir entity.
     *
     * @Route("/{id}/edit", name="factureavoir_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, FactureAvoir $factureAvoir) {
        if ($factureAvoir->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Cette facture avoir est terminé, on ne peut pas la modifier.');
            return $this->redirectToRoute('factureavoir_show', array('id' => $factureAvoir->getId()));
        }
        $facture = $factureAvoir->getFacture();
        if ($facture->getRegle() < $facture->getTotal()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut régler la facture pour faire un avoir');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }

        $editForm = $this->createForm('App\Form\FactureAvoirType', $factureAvoir);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $maxAvoire = 0;
            foreach ($factureAvoir->getLigneFactureAvoirs() as $key => $ligne) {
                $maxInitiale = $ligne->getQteMax();
                //qte des avoirs de cette article sur cette facture
                $qb = $em->getRepository('App\\Entity\\LigneFactureAvoir')->createQueryBuilder('lfa');
                $qb->select('sum(lfa.qte) as somme')
                        ->join('lfa.factureAvoir', 'fa')
                        ->join('fa.facture', 'f')
                        ->where('f.id=' . $facture->getId())
                        ->andWhere('fa.termine=true')
                        ->andWhere('lfa.article=' . $ligne->getArticle()->getId());
                $query = $qb->getQuery();
                $maxAvoire = $query->getSingleScalarResult();
                // die($ligne->getQte()." - ".$maxInitiale." - ".$maxAvoire);
                if ($ligne->getQte() > $maxInitiale - $maxAvoire) {
                    $valuer = $maxInitiale - $maxAvoire;
                    $msg = $valuer === 0 ? "Quantité dépassé" : "Cette valeur doit être inférieur ou égale à " . $valuer;
                    $editForm->get('ligneFactureAvoirs')[$key]->get('qte')->addError(new FormError($msg));
                }
            }
            if ($editForm->isValid()) {
                $em->flush();
                return $this->redirectToRoute('factureavoir_edit', array('id' => $factureAvoir->getId()));
            }
        }

        return $this->render('factureavoir/facture/edit.html.twig', array(
                    'factureAvoir' => $factureAvoir,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to print an existing devi entity.
     *
     * @Route("/{id}/print", name="factureavoir_print", methods={"GET"})
     */
    public function printAction(FactureAvoir $factureAvoir, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);

        $totalDinars = intval($factureAvoir->getTotal());
        $totalMillimesEnTtLettres = explode('.', number_format($factureAvoir->getTotal() - intval($factureAvoir->getTotal()), 3))[1];
        $totalDinarsEnTtLettres = (new Numbers_Words())->toWords($totalDinars, $request->getLocale());
        return $this->render('factureavoir/print.html.twig', array(
                    'factureAvoir' => $factureAvoir,
                    'societe' => $societe,
                    'totalDinarsEnTtLettres' => $totalDinarsEnTtLettres,
                    'totalMillimesEnTtLettres' => $totalMillimesEnTtLettres
        ));
    }

}

<?php

namespace App\Controller;

use App\Entity\BonLivraison;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneReglementBonLivraison;
use App\Entity\Facture;
use App\Entity\LigneFacture;
use Symfony\Component\HttpFoundation\JsonResponse;
use Numbers_Words;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Bonlivraison controller.
 *
 * @Route("bonlivraison")
 */
class BonLivraisonController extends BaseController {

    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('client')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $client = $em->getRepository('App\\Entity\\Client')->findOneById($request->query->get('client'));
            $chaine .= 'Code client : ' . $client->getCode();
        }
        if ($request->query->get('termine')==='2') {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Etat : Terminé';
        }elseif($request->query->get('termine')==='1'){
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Etat : En Cours';
        }
        if ($request->query->get('regle')) {
            $regle = $request->query->get('regle');
            switch ($regle) {
                case '1':
                    $i > 0 ? $chaine .= ',' : $i++;
                    $chaine .= 'Réglé';
                    break;
                case '2':
                    $i > 0 ? $chaine .= ',' : $i++;
                    $chaine .= 'Réglé : En Cours';
                    break;
                case '3':
                    $i > 0 ? $chaine .= ',' : $i++;
                    $chaine .= 'Non Réglé';
                    break;
            }
        }
        if ($request->query->get('facture')==='2') {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Facturé';
        }elseif($request->query->get('facture')==='1'){
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Non facturé';
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
        if ($request->query->get('startDateLivraison')) {
            $startDateLivraison = $dateFormat->formatDate($request->query->get('startDateLivraison'));
            if ($startDateLivraison) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date livraison >= ' . str_replace('/', '-', $request->query->get('startDateLivraison'));
            }
        }
        if ($request->query->get('endDateLivraison')) {
            $endDateLivraison = $dateFormat->formatDate($request->query->get('endDateLivraison'));
            if ($endDateLivraison) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date livraison <= ' . str_replace('/', '-', $request->query->get('endDateLivraison'));
            }
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\BonLivraison')->createQueryBuilder('a');
        $qb->join('a.client', 'clt');
        //filter
        $code = null;
        if ($request->query->get('code')) {
            $code = $request->query->get('code');
            $qb->where('a.code like :code')->setParameter('code', '%' . $code . '%');
        }
        $client = null;
        if ($request->query->get('client')) {
            $client = $request->query->get('client');
            $qb->andWhere('a.client = :client')->setParameter('client', $client);
        }
        $termine = null;
        if ($request->query->get('termine')) {
            $termine = $request->query->get('termine');
            $qb->andWhere('a.termine = :termine')->setParameter('termine', $termine - 1);
        }
        $facture = null;
        if ($request->query->get('facture')) {
            $facture = $request->query->get('facture');
            if($facture ==='1'){
                $qb->andWhere('a.converted is NULL');
            }else{
                $qb->andWhere('a.converted = :facture')->setParameter('facture', $facture - 1);
            }
        }
        $regle = null;
        if ($request->query->get('regle')) {
            $regle = $request->query->get('regle');
            switch ($regle) {
                case '1':
                    $qb->andWhere('a.regle = a.total');
                    break;
                case '2':
                    $qb->andWhere('a.regle < a.total')
                        ->andWhere('a.regle >0');
                    break;
                case '3':
                    $qb->andWhere('a.regle = 0');
                    break;
            }
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
        $startDateLivraison = null;
        if ($request->query->get('startDateLivraison')) {
            $startDateLivraison = $dateFormat->formatDate($request->query->get('startDateLivraison'));
            if ($startDateLivraison) {
                $qb->andWhere('a.dateLivraison >= :startDateLivraison')->setParameter('startDateLivraison', $startDateLivraison);
            }
        }
        $endDateLivraison = null;
        if ($request->query->get('endDateLivraison')) {
            $endDateLivraison = $dateFormat->formatDate($request->query->get('endDateLivraison'));
            if ($endDateLivraison) {
                $qb->andWhere('a.dateLivraison <= :endDateLivraison')->setParameter('endDateLivraison', $endDateLivraison);
            }
        }
        return $qb;
    }

    /**
     * Lists all bonLivraison entities.
     *
     * @Route("/", name="bonlivraison_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);

        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $clients = $em->getRepository('App\\Entity\\Client')->findAll();
        return $this->render('bonlivraison/index.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients
        ));
    }

    /**
     * Totaux bonLivraison.
     *
     * @Route("/totaux/{code}/{client}/{termine}/{regle}/{facture}/{startDateCreation}/{endDateCreation}/{startDateLivraison}/{endDateLivraison}", name="bonlivraison_totaux", methods={"GET"})
     */
    public function totauxBlAction(Request $request, $code = null, $client = null, $termine = null,$regle = null,$facture = null,$startDateCreation = null, $endDateCreation = null, $startDateLivraison = null, $endDateLivraison = null) {
        $request->query->set('code', $code);
        $request->query->set('client', $client);
        $request->query->set('termine', $termine);
        $request->query->set('regle', $regle);
        $request->query->set('facture', $facture);
        $request->query->set('startDateCreation', $startDateCreation);
        $request->query->set('endDateCreation', $endDateCreation);
        $request->query->set('startDateLivraison', $startDateLivraison);
        $request->query->set('endDateLivraison', $endDateLivraison);
        $em = $this->getDoctrine()->getManager();
        $qbTous = $this->getQbByParametres($em, $request)
                ->select('sum(a.total) as total')
                ->addSelect('sum(a.regle) as regle')
                ->getQuery()
                ->getScalarResult();
        $qbFacture = $this->getQbByParametres($em, $request)
                ->addSelect('sum(a.regle) as regle')
                ->andWhere('a.converted = 1')
                ->getQuery()
                ->getScalarResult();
        $qbNonFacture = $this->getQbByParametres($em, $request)
                ->addSelect('sum(a.regle) as regle')
                ->addSelect('sum(a.reste) as reste')
                ->andWhere('a.converted is NULL')
                ->getQuery()
                ->getScalarResult();
        
        $totalTous = $qbTous[0]['total'];//total des tous
        $regleTous = $qbTous[0]['regle'];//reglé des tous
        $regleFacture = $qbFacture[0]['regle'];//reglé facture
        $regleNonFacture = $qbNonFacture[0]['regle'];//regle non facture
        $resteNonFacture = $qbNonFacture[0]['reste'];//reste non facture
        return $this->render('_partial/totaux.html.twig', array(
                    'totalTous' => $totalTous,
                    'regleTous' => $regleTous,
                    'regleFacture' => $regleFacture,
                    'regleNonFacture' => $regleNonFacture,
                    'resteNonFacture' => $resteNonFacture,
        ));
    }

    /**
     * Lists all bonlivraison entities print.
     *
     * @Route("/blglobal", name="blglobal", methods={"GET"})
     */
    public function blglobalAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);
        $client = null;
        if ($request->query->get('client')) {
            $client = $em->getRepository('App\\Entity\\Client')->findOneById($request->query->get('client'));
        }
        return $this->render('bonlivraison/blglobal.html.twig', array(
                    'bls' => $qb->getQuery()->getResult(),
                    'societe' => $societe,
                    'client' => $client,
                    'code' => $request->query->get('client'),
                    'startDateCreation' => $request->query->get('startDateCreation'),
                    'endDateCreation' => $request->query->get('endDateCreation'),
                    'startDateLivraison' => $request->query->get('startDateLivraison'),
                    'endDateLivraison' => $request->query->get('endDateLivraison')
        ));
    }

    /**
     * Lists all bonlivraison entities.
     *
     * @Route("/export/xls", name="bonlivraison_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'bonlivraison' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des bon de livraisons ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Code')
                ->setCellValue('B4', 'Passager/Client')
                ->setCellValue('C4', 'Code Client')
                ->setCellValue('D4', 'Raison social Client')
                ->setCellValue('E4', 'Nom')
                ->setCellValue('F4', 'Cin')
                ->setCellValue('G4', 'Total HT')
                ->setCellValue('H4', 'Total Remise')
                ->setCellValue('I4', 'Total TVA')
                ->setCellValue('J4', 'Total TTC')
                ->setCellValue('K4', 'Total Reglé')
                ->setCellValue('L4', 'Total Reste')
                ->setCellValue('M4', 'Terminé')
                ->setCellValue('N4', 'Date création')
                ->setCellValue('O4', 'Date livraison')
                ->setCellValue('P4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A' . $i, $entity->getCode())
                    ->setCellValue('B' . $i, $entity->getClient()->getPassager() ? 'Passager' : 'Client')
                    ->setCellValue('C' . $i, $entity->getClient()->getCode())
                    ->setCellValue('D' . $i, $entity->getClient()->getRs())
                    ->setCellValue('E' . $i, $entity->getNom())
                    ->setCellValue('F' . $i, $entity->getCin())
                    ->setCellValue('G' . $i, $entity->getHt())
                    ->setCellValue('H' . $i, $entity->getRemise())
                    ->setCellValue('I' . $i, $entity->getTva())
                    ->setCellValue('J' . $i, $entity->getTotal())
                    ->setCellValue('K' . $i, $entity->getRegle())
                    ->setCellValue('L' . $i, $entity->getReste() < 0 ? '0' : $entity->getReste())
                    ->setCellValue('M' . $i, $entity->getTermine() ? 'Terminé' : 'En cours')
                    ->setCellValue('N' . $i, $entity->getDateCreation() ? $entity->getDateCreation()->format('d-m-Y') : '')
                    ->setCellValue('O' . $i, $entity->getDateLivraison() ? $entity->getDateLivraison()->format('d-m-Y') : '')
                    ->setCellValue('P' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Bon de livraisons');
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
     * Creates a new bonLivraison entity.
     *
     * @Route("/new", name="bonlivraison_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        $bonLivraison = new Bonlivraison();
        $form = $this->createForm('App\Form\BonLivraisonType', $bonLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($bonLivraison);
            $em->flush($bonLivraison);
            if ($form->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('bonlivraison_print', array('id' => $bonLivraison->getId()));
            }
            return $this->redirectToRoute('bonlivraison_show', array('id' => $bonLivraison->getId()));
        }

        return $this->render('bonlivraison/new.html.twig', array(
                    'bonLivraison' => $bonLivraison,
                    'form' => $form->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * @Route("/{id}/show",name="bonlivraison_show", methods={"GET","POST"})
     */
    public function showAction(Request $request, BonLivraison $bonlivraison) {

        $form_regler = $this->createFormBuilder($bonlivraison)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndRegler', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la bon de livraison et régler', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_imprimer = $this->createFormBuilder($bonlivraison)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndImprimer', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la bon de livraison et imprimer', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_converter = $this->createFormBuilder($bonlivraison)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndConverter', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la bon de livraison et converter', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_regler->handleRequest($request);
        $form_imprimer->handleRequest($request);
        $form_converter->handleRequest($request);
        if ($form_regler->isSubmitted() && $form_regler->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('bonlivraison_reglements', array('id' => $bonlivraison->getId()));
        }
        if ($form_imprimer->isSubmitted() && $form_imprimer->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('bonlivraison_print', array('id' => $bonlivraison->getId()));
        }
        if ($form_converter->isSubmitted() && $form_converter->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('bonlivraison_to_facture', array('id' => $bonlivraison->getId()));
        }
        return $this->render('bonlivraison/show.html.twig', array(
                    'bonlivraison' => $bonlivraison,
                    'form_regler' => $form_regler->createView(),
                    'form_imprimer' => $form_imprimer->createView(),
                    'form_converter' => $form_converter->createView()
        ));
    }

    /**
     * Displays a form to edit an existing bonLivraison entity.
     *
     * @Route("/{id}/edit", name="bonlivraison_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BonLivraison $bonLivraison) {
        $em = $this->getDoctrine()->getManager();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        $originalLigneBonLivraisons = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($bonLivraison->getLigneBonLivraisons() as $ligne) {
            $originalLigneBonLivraisons->add($ligne);
        }
        $editForm = $this->createForm('App\Form\BonLivraisonType', $bonLivraison, array(
            'disabled' => $bonLivraison->getRegle() > 0
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($originalLigneBonLivraisons as $ligne) {
                if (false === $bonLivraison->getLigneBonLivraisons()->contains($ligne)) {
                    $ligne->setBonLivraison(null);
                    $em->persist($ligne);
                    $em->remove($ligne);
                }
            }
            $em->flush();
            if ($editForm->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('bonlivraison_print', array('id' => $bonLivraison->getId()));
            }
            return $this->redirectToRoute('bonlivraison_show', array('id' => $bonLivraison->getId()));
        }

        return $this->render('bonlivraison/edit.html.twig', array(
                    'bonLivraison' => $bonLivraison,
                    'edit_form' => $editForm->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * Displays a form to print an existing bonlivraison entity.
     *
     * @Route("/{id}/print", name="bonlivraison_print", methods={"GET"})
     */
    public function printAction(BonLivraison $bonlivraison, Request $request, \App\Service\PdfGenerator $pdf, \App\Service\DocumentCalculator $calc) {
        $em = $this->getDoctrine()->getManager();
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);

        $totalDinars = intval($bonlivraison->getTotal());
        $totalMillimesEnTtLettres = explode('.', number_format($bonlivraison->getTotal() - intval($bonlivraison->getTotal()), 3))[1];
        $totalDinarsEnTtLettres = (new Numbers_Words())->toWords($totalDinars, $request->getLocale());

        return $pdf->renderResponse('bonlivraison/pdf.html.twig', [
            'bonlivraison' => $bonlivraison,
            'societe' => $societe,
            'logoPath' => $this->societeLogoPath($societe),
            'tvaBreakdown' => $calc->tvaBreakdown($bonlivraison->getLigneBonLivraisons()),
            'totalDinarsEnTtLettres' => $totalDinarsEnTtLettres,
            'totalMillimesEnTtLettres' => $totalMillimesEnTtLettres,
        ], 'BL-' . $bonlivraison->getCode());
    }

    /**
     * Reglements
     * 
     * @Route("/{id}/reglements",name="bonlivraison_reglements", methods={"GET","POST"})
     */
    public function reglementsAction(Request $request, BonLivraison $bonlivraison) {
        if (!$bonlivraison->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut terminer la bon de livraison pour faire un réglement');
            return $this->redirectToRoute('bonlivraison_show', array('id' => $bonlivraison->getId()));
        }
        $em = $this->getDoctrine()->getManager();
        $ligneReglement = new LigneReglementBonLivraison();
        $ligneReglement->setBonLivraison($bonlivraison);
        $ligneReglement->setType('reglement');

        $form = $this->createForm('App\Form\LigneReglementBonLivraisonType', $ligneReglement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ligneReglement);
            $em->flush($ligneReglement);
            return $this->redirectToRoute('bonlivraison_reglements', array('id' => $bonlivraison->getId()));
        }
        $ligneReglements = $em->getRepository('App\\Entity\\LigneReglementBonLivraison')->findByBonLivraison($bonlivraison);
        return $this->render('bonlivraison/reglements.html.twig', array(
                    'bonlivraison' => $bonlivraison,
                    'ligneReglements' => $ligneReglements,
                    'ligneReglement' => $ligneReglement,
                    'form' => $form->createView(),
        ));
    }

    /**
     * delete reglement
     * 
     * @Route("/{id}/reglement/{idLigneReglement}/delete",name="bonlivraison_reglement_delete", methods={"GET"})
     */
    public function reglementDeleteAction($id, $idLigneReglement) {
        $em = $this->getDoctrine()->getManager();
        $ligneReglement = $em->getRepository('App\\Entity\\LigneReglementBonLivraison')->find($idLigneReglement);
        $em->remove($ligneReglement);
        $em->flush($ligneReglement);
        return $this->redirectToRoute('bonlivraison_reglements', array('id' => $id));
    }

    /**
     * Convert bonlivraison to facture
     * @Route("/{id}/facture",name="bonlivraison_to_facture", methods={"GET"})
     */
    public function bonlivraisonToFacture(BonLivraison $bonlivraison) {
        if (!$bonlivraison->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut terminer la bon de livraison pour converter en facture');
            return $this->redirectToRoute('bonlivraison_show', array('id' => $bonlivraison->getId()));
        }
        if ($bonlivraison->getConverted()) {
            $this->get('session')->getFlashBag()->add('info', 'Cette bon de livraison est déja converti en facture');
            return $this->redirectToRoute('bonlivraison_show', array('id' => $bonlivraison->getId()));
        }
        $em = $this->getDoctrine()->getManager();
        $facture = new Facture();
        $facture->setFromBl(true);
        $facture->setClient($bonlivraison->getClient());
        $facture->setHt($bonlivraison->getHt());
        $facture->setNote($bonlivraison->getNote());
        $facture->setRemise($bonlivraison->getRemise());
        $facture->setTermine(true);
        if ($bonlivraison->getRegle() === $bonlivraison->getTotal()) {
            $facture->setRegle($bonlivraison->getRegle() + 0.5);
        } else {
            $facture->setRegle($bonlivraison->getRegle());
        }
        $facture->setTotal($bonlivraison->getTotal() + 0.5);
        $facture->setTva($bonlivraison->getTva());
        $em->persist($facture);
        $em->flush($facture);
        $facture->setReste($bonlivraison->getReste());
        $em->flush($facture);
        foreach ($bonlivraison->getLigneBonLivraisons() as $ligne) {
            $lf = new LigneFacture();
            $lf->setArticle($ligne->getArticle());
            $lf->setDesignation($ligne->getDesignation());
            $lf->setPrixUnitaire($ligne->getPrixUnitaire());
            $lf->setQte($ligne->getQte());
            $lf->setRemise($ligne->getRemise());
            $lf->setTtc($ligne->getTtc());
            $lf->setTva($ligne->getTva());
            $lf->setFacture($facture);
            $em->persist($lf);
            $em->flush($lf);
        }
        $bonlivraison->setConverted(true);
        $em->flush($bonlivraison);
        return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
    }

    /**
     * @Route("/checked/add",name="addBlToChecked", methods={"GET"})
     */
    public function addBlToCheckedAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $session = $this->get('session');
        if (!$session->has('bls')) {
            $session->set('bls', []);
        }
        $bls = $session->get('bls');
        if (!in_array($request->query->get('code'), $bls)) {
            array_push($bls, $request->query->get('code'));
            $session->set('bls', $bls);
        }
        return $this->getBlsArray($bls);
    }

    /**
     * @Route("/checked/delete",name="deleteBlFromChecked", methods={"GET"})
     */
    public function deleteBlFromCheckedAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $session = $this->get('session');
        $bls = $session->get('bls');
        $key = array_search($request->query->get('code'), $bls);
        array_splice($bls, $key, 1);
        $session->set('bls', $bls);
        if (count($bls) === 0) {
            $session->remove('idClient');
            $myresponse = array(
                'success' => false,
                'content' => 'Pas du bons de livraison sélectionnés'
            );
            return new JsonResponse($myresponse);
        }
        return $this->getBlsArray($bls);
    }

    /**
     * @Route("/checked/all",name="getBlsChecked", methods={"GET"})
     */
    public function getBlsCheckedAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $session = $this->get('session');
        if (!$session->has('bls')) {
            $myresponse = array(
                'success' => false,
                'content' => 'Pas du bons de livraison sélectionnés'
            );
            return new JsonResponse($myresponse);
        }
        $bls = $session->get('bls');
        if (count($bls) === 0) {
            $myresponse = array(
                'success' => false,
                'content' => 'Pas du bons de livraison sélectionnés'
            );
            return new JsonResponse($myresponse);
        } else {
            return $this->getBlsArray($bls);
        }
    }

    /**
     * @Route("/checked/convert",name="convertirBlsEnFacture", methods={"GET"})
     */
    public function convertirBlsEnFactureAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $session = $this->get('session');
        if (!$session->has('bls')) {
            $myresponse = array(
                'success' => false,
                'content' => 'Pas du bons de livraison sélectionnés'
            );
            return new JsonResponse($myresponse);
        }
        $bls = $session->get('bls');
        if (count($bls) > 0) {
            $em = $this->getDoctrine()->getManager();
            $facture = new Facture();
            $facture->setFromBl(true);
            $facture->setTermine(true);
            $client = null;
            $note = 'Facture des bons de livraison : ';
            $ht = 0;
            $remise = 0;
            $total = 0;
            $tva = 0;
            $regle = 0;
            $i = 0;
            foreach ($bls as $bl) {
                $bonlivraison = $em->getRepository('App\\Entity\\BonLivraison')->findOneByCode($bl);
                if (!$bonlivraison->getConverted()) {
                    foreach ($bonlivraison->getLigneBonLivraisons() as $ligne) {
                        $lf = new LigneFacture();
                        $lf->setArticle($ligne->getArticle());
                        $lf->setDesignation($ligne->getDesignation());
                        $lf->setPrixUnitaire($ligne->getPrixUnitaire());
                        $lf->setQte($ligne->getQte());
                        $lf->setRemise($ligne->getRemise());
                        $lf->setTtc($ligne->getTtc());
                        $lf->setTva($ligne->getTva());
                        $lf->setFacture($facture);
                        $em->persist($lf);
                        $em->flush($lf);
                    }
                    $client = $bonlivraison->getClient();
                    $ht += $bonlivraison->getHt();
                    $remise += $bonlivraison->getRemise();
                    $tva += $bonlivraison->getTva();
                    $total += $bonlivraison->getTotal();
                    $regle += $bonlivraison->getRegle();
                    if ($i == 0) {
                        $note .= $bl;
                    } else {
                        $note .= ', ' . $bl;
                    }
                    $i++;
                    $bonlivraison->setConverted(true);
                    $em->flush($bonlivraison);
                }
            }
            $facture->setClient($client);
            $facture->setHt($ht);
            $facture->setNote($note);
            $facture->setRemise($remise);
            if ($regle === $total) {
                $facture->setRegle($regle + 0.5);
            } else {
                $facture->setRegle($regle);
            }
            $facture->setTotal($total + 0.5);
            $facture->setReste($total - $regle);
            $facture->setTva($tva);
            $em->persist($facture);
            $em->flush($facture);
            $session->remove('idClient');
            $session->remove('bls');
            $response = array(
                'success' => true,
                'id' => $facture->getId()
            );
            return new JsonResponse($response);
        } else {
            $response = array(
                'success' => false,
                'content' => 'Pas de bons de livraison sélectionnés'
            );
            return new JsonResponse($response);
        }
    }

    function dismount($object) {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($object);
            $property->setAccessible(false);
        }
        return $array;
    }

    public function getBlsArray($bls) {
        if ($bls instanceof Object) {
            $bls = $this->dismount($bls);
        }
        $session = $this->get('session');
        $session->set('bls', $bls);
        $serializer = $this->container->get('serializer');
        $response = array(
            'success' => true,
            'bls' => $serializer->serialize($bls, 'json')
        );
        return new JsonResponse($response);
    }

    private function deleteFunction($em, $entity) {
        $em->remove($entity);
        $em->flush();
    }

    /**
     * Deletes a bonlivraison entity.
     *
     * @Route("/{id}/delete", name="bonlivraison_delete", methods={"GET"})
     */
    public function deleteAction(BonLivraison $bonlivraison) {
        $em = $this->getDoctrine()->getManager();
        $ligneBonLivraisons = $bonlivraison->getLigneBonLivraisons();
        foreach ($ligneBonLivraisons as $ligne) {
            $qteEnStock = $ligne->getArticle()->getQteEnStock();
            $qteAajouter = $ligne->getQte();
            $article = $em->getRepository('App\\Entity\\Article')->findOneById($ligne->getArticle()->getId());
            $article->setQteEnStock($qteEnStock + $qteAajouter); //ajout du stock en article
            $em->flush($article);
            $this->deleteFunction($em, $ligne); //suppression de ligne livraison
        }
        $ligne_reglement_bon_livraisons = $em->getRepository('App\\Entity\\LigneReglementBonLivraison')->findByBonLivraison($bonlivraison);
        foreach ($ligne_reglement_bon_livraisons as $ligne) {
            $this->deleteFunction($em, $ligne); //suppression des reglements
        }
        $stocks = $em->getRepository('App\\Entity\\Stock')->findByDesignation($bonlivraison->getCode());
        foreach ($stocks as $ligne) {
            $this->deleteFunction($em, $ligne); //suppression des mouvements
        }
        $this->deleteFunction($em, $bonlivraison);
        return $this->redirectToRoute('bonlivraison_index');
    }

}

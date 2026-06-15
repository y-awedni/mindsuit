<?php

namespace App\Controller;

use App\Entity\BonReception;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneReglementBonReception;
use Numbers_Words;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Bonreception controller.
 *
 * @Route("bonreception")
 */
class BonReceptionController extends BaseController {

    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('fournisseur')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $fournisseur = $em->getRepository('App\\Entity\\Fournisseur')->findOneById($request->query->get('fournisseur'));
            $chaine .= 'Code fournisseur : ' . $fournisseur->getCode();
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
        if ($request->query->get('startDateReception')) {
            $startDateReception = $dateFormat->formatDate($request->query->get('startDateReception'));
            if ($startDateReception) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date réception >= ' . str_replace('/', '-', $request->query->get('startDateReception'));
            }
        }
        if ($request->query->get('endDateReception')) {
            $endDateReception = $dateFormat->formatDate($request->query->get('endDateReception'));
            if ($endDateReception) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date réception <= ' . str_replace('/', '-', $request->query->get('endDateReception'));
            }
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\BonReception')->createQueryBuilder('a');
        $qb->join('a.fournisseur', 'frs');
        //filter
        if ($request->query->get('code')) {
            $qb->where('a.code like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        $fournisseur = null;
        if ($request->query->get('fournisseur')) {
            $fournisseur = $request->query->get('fournisseur');
            $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);
        }
        $termine = null;
        if ($request->query->get('termine')) {
            $termine = $request->query->get('termine');
            $qb->andWhere('a.termine = :termine')->setParameter('termine', $termine - 1);
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
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($startDateCreation) {
                $qb->andWhere('a.createdAt >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
            }
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($endDateCreation) {
                $qb->andWhere('a.createdAt <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
            }
        }
        if ($request->query->get('startDateReception')) {
            $startDateReception = $dateFormat->formatDate($request->query->get('startDateReception'));
            if ($startDateReception) {
                $qb->andWhere('a.dateReception >= :startDateReception')->setParameter('startDateReception', $startDateReception);
            }
        }
        if ($request->query->get('endDateReception')) {
            $endDateReception = $dateFormat->formatDate($request->query->get('endDateReception'));
            if ($endDateReception) {
                $qb->andWhere('a.dateReception <= :endDateReception')->setParameter('endDateReception', $endDateReception);
            }
        }
        return $qb;
    }

    /**
     * Lists all bonReception entities.
     *
     * @Route("/", name="bonreception_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getEm();
        $qb = $this->getQbByParametres($em, $request);
        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $boncommandes = $em->getRepository('App\\Entity\\BonCommandeFrs')->findAll();
        $fournisseurs = $em->getRepository('App\\Entity\\Fournisseur')->findAll();
        return $this->render('bonreception/index.html.twig', array(
                    'pagination' => $pagination,
                    'boncommandes' => $boncommandes,
                    'fournisseurs' => $fournisseurs
        ));
    }

    /**
     * Totaux facture.
     *
     * @Route("/totaux/{code}/{fournisseur}/{termine}/{regle}/{startDateCreation}/{endDateCreation}/{startDateReception}/{endDateReception}", name="facture_totaux", methods={"GET"})
     */
    public function totauxBrAction(Request $request,$code = null, $fournisseur = null, $termine = null,$regle = null, $startDateCreation = null, $endDateCreation = null, $startDateReception = null, $endDateReception = null) {
        $request->query->set('code', $code);
        $request->query->set('fournisseur', $fournisseur);
        $request->query->set('termine', $termine);
        $request->query->set('regle', $regle);
        $request->query->set('startDateCreation', $startDateCreation);
        $request->query->set('endDateCreation', $endDateCreation);
        $request->query->set('startDateEcheance', $startDateReception);
        $request->query->set('endDateEcheance', $endDateReception);
        $em= $this->getEm();
        $qb = $this->getQbByParametres($em, $request)
                ->select('sum(a.total) as total')
                ->addSelect('sum(a.regle) as regle')
                ->getQuery()
                ->getScalarResult();
        $total=$qb[0]['total'];
        $regle=$qb[0]['regle'];
        return $this->render('_partial/totaux.html.twig', array(
                    'total' => $total,
                    'regle' => $regle
        ));
    }
    
    /**
     * Lists all article entities.
     *
     * @Route("/export/xls", name="bonreception_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getEm();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'bonreception' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);

        $sheet->setCellValue('A1', 'Liste des bon de réceptions ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Réf')
                ->setCellValue('B4', 'Code fournisseur')
                ->setCellValue('C4', 'Raison social fournisseur')
                ->setCellValue('D4', 'Total HT')
                ->setCellValue('E4', 'Total REMISE')
                ->setCellValue('F4', 'Total TVA')
                ->setCellValue('G4', 'Total TTC')
                ->setCellValue('H4', 'Réglé')
                ->setCellValue('I4', 'Reste')
                ->setCellValue('J4', 'Date création')
                ->setCellValue('K4', 'Date réception')
                ->setCellValue('L4', 'Réf bon de commande')
                ->setCellValue('M4', 'Terminé')
                ->setCellValue('N4', 'Note');

        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
        $i = 5;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A' . $i, $entity->getCode())
                    ->setCellValue('B' . $i, $entity->getFournisseur()->getCode())
                    ->setCellValue('C' . $i, $entity->getFournisseur()->getRs())
                    ->setCellValue('D' . $i, $entity->getHt())
                    ->setCellValue('E' . $i, $entity->getRemise())
                    ->setCellValue('F' . $i, $entity->getTva())
                    ->setCellValue('G' . $i, $entity->getTotal())
                    ->setCellValue('H' . $i, $entity->getRegle())
                    ->setCellValue('I' . $i, $entity->getReste() < 0 ? '0' : $entity->getReste())
                    ->setCellValue('J' . $i, $entity->getCreatedAt() ? $entity->getCreatedAt()->format('d-m-Y') : '')
                    ->setCellValue('K' . $i, $entity->getDateReception() ? $entity->getDateReception()->format('d-m-Y') : '')
                    ->setCellValue('L' . $i, $entity->getBonCommande())
                    ->setCellValue('M' . $i, $entity->getTermine() ? 'Terminé' : 'En cours')
                    ->setCellValue('N' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Liste des bon de réceptions');
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
     * Creates a new bonreception_boncommande entity.
     *
     * @Route("/boncommande/new/id", name="bonreception_boncommande_new_id", methods={"GET", "POST"})
     */
    public function bonreceptionBoncommandeNewIdAction(Request $request) {

        if ($request->query->get('id')) {
            return $this->redirectToRoute('bonreception_boncommande_new', array('id' => $request->query->get('id')));
        }
        $this->get('session')->getFlashBag()->add('info', 'Il faut sélectionner bon de commande pour faire un bon de réception');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Creates a new bonreception_boncommande entity.
     *
     * @Route("/boncommande/new", name="bonreception_boncommande_new", methods={"GET", "POST"})
     */
    public function bonreceptionBoncommandeNewAction(Request $request) {
        $em = $this->getEm();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        $bonReception = new BonReception();
        $bonCommandeId = $request->query->get('id');
        $bonCommande = $em->getRepository('App\\Entity\\BonCommandeFrs')->findOneById($bonCommandeId);
        $bonReception->setBonCommande($bonCommande);
        $bonReception->setFournisseur($bonCommande->getFournisseur());
        foreach ($bonCommande->getLigneBonCommandeFrss() as $ligneBc) {
            $ligneBr = new \App\Entity\LigneBonReception();
            $ligneBr->setArticle($ligneBc->getArticle());
            $ligneBr->setQte($ligneBc->getQte());
            $ligneBr->setDesignation($ligneBc->getDesignation());
            $bonReception->addLigneBonReception($ligneBr);
        }
        $form = $this->createForm('App\Form\BonReceptionType', $bonReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $bonReception->setBonCommande($bonCommande);
            $em->persist($bonReception);
            $em->flush($bonReception);

            if ($form->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('bonreception_print', array('id' => $bonReception->getId()));
            }
            return $this->redirectToRoute('bonreception_show', array('id' => $bonReception->getId()));
        }

        return $this->render('bonreception/new.html.twig', array(
                    'bonReception' => $bonReception,
                    'form' => $form->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * Creates a new bonReception entity.
     *
     * @Route("/new", name="bonreception_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getEm();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        $bonReception = new BonReception();
        $form = $this->createForm('App\Form\BonReceptionType', $bonReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($bonReception);
            $em->flush($bonReception);

            if ($form->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('bonreception_print', array('id' => $bonReception->getId()));
            }
            return $this->redirectToRoute('bonreception_show', array('id' => $bonReception->getId()));
        }

        return $this->render('bonreception/new.html.twig', array(
                    'bonReception' => $bonReception,
                    'form' => $form->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * @Route("/{id}/show",name="bonreception_show", methods={"GET","POST"})
     */
    public function showAction(Request $request, BonReception $bonreception) {
        $form_regler = $this->createFormBuilder($bonreception)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndRegler', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la bon de réception et régler', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_imprimer = $this->createFormBuilder($bonreception)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndImprimer', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la bon de réception et imprimer', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_regler->handleRequest($request);
        $form_imprimer->handleRequest($request);
        if ($form_regler->isSubmitted() && $form_regler->isValid()) {
            $em = $this->getEm();
            $em->flush();
            return $this->redirectToRoute('bonreception_reglements', array('id' => $bonreception->getId()));
        }
        if ($form_imprimer->isSubmitted() && $form_imprimer->isValid()) {
            $em = $this->getEm();
            $em->flush();
            return $this->redirectToRoute('bonreception_print', array('id' => $bonreception->getId()));
        }
        return $this->render('bonreception/show.html.twig', array(
                    'bonReception' => $bonreception,
                    'form_regler' => $form_regler->createView(),
                    'form_imprimer' => $form_imprimer->createView()
        ));
    }

    /**
     * Displays a form to edit an existing bonReception entity.
     *
     * @Route("/{id}/edit", name="bonreception_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BonReception $bonReception) {
        $em = $this->getEm();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        $bonCommande = $bonReception->getBonCommande();
        if ($bonReception->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Cette bon de réception est terminé, on ne peut pas la modifier.');
            return $this->redirectToRoute('bonreception_show', array('id' => $bonReception->getId()));
        }
        $originalLigneBonReceptions = new ArrayCollection();
        foreach ($bonReception->getLigneBonReceptions() as $ligne) {
            $originalLigneBonReceptions->add($ligne);
        }
        $editForm = $this->createForm('App\Form\BonReceptionType', $bonReception);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getEm();
            $bonReception->setBonCommande($bonCommande);
            foreach ($originalLigneBonReceptions as $ligne) {
                if (false === $bonReception->getLigneBonReceptions()->contains($ligne)) {
                    $ligne->setBonReception(null);
                    $em->persist($ligne);
                    $em->remove($ligne);
                }
            }
            $em->flush();
            if ($editForm->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('bonreception_print', array('id' => $bonReception->getId()));
            }
            return $this->redirectToRoute('bonreception_show', array('id' => $bonReception->getId()));
        }
        return $this->render('bonreception/edit.html.twig', array(
                    'bonReception' => $bonReception,
                    'edit_form' => $editForm->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * Displays a form to print an existing bnoreception entity.
     *
     * @Route("/{id}/print", name="bonreception_print", methods={"GET"})
     */
    public function printAction(BonReception $bonreception, Request $request, \App\Service\PdfGenerator $pdf, \App\Service\DocumentCalculator $calc) {
        $em = $this->getEm();
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);

        $totalDinars = intval($bonreception->getTotal());
        $totalMillimesEnTtLettres = explode('.', number_format($bonreception->getTotal() - intval($bonreception->getTotal()), 3))[1];
        $totalDinarsEnTtLettres = (new Numbers_Words())->toWords($totalDinars, $request->getLocale());

        return $pdf->renderResponse('bonreception/pdf.html.twig', [
            'bonreception' => $bonreception,
            'societe' => $societe,
            'logoPath' => $this->societeLogoPath($societe),
            'tvaBreakdown' => $calc->tvaBreakdown($bonreception->getLigneBonReceptions()),
            'totalDinarsEnTtLettres' => $totalDinarsEnTtLettres,
            'totalMillimesEnTtLettres' => $totalMillimesEnTtLettres,
        ], 'BR-' . $bonreception->getCode());
    }

    /**
     * Reglements
     * 
     * @Route("/{id}/reglements",name="bonreception_reglements", methods={"GET","POST"})
     */
    public function reglementsAction(Request $request, BonReception $bonreception) {
        if (!$bonreception->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut terminer la bon de reception pour faire un réglement');
            return $this->redirectToRoute('bonreception_show', array('id' => $bonreception->getId()));
        }
        $em = $this->getEm();
        $ligneReglement = new LigneReglementBonReception();
        $ligneReglement->setBonReception($bonreception);
        $ligneReglement->setType('reglement');

        $form = $this->createForm('App\Form\LigneReglementBonReceptionType', $ligneReglement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ligneReglement);
            $em->flush($ligneReglement);
            return $this->redirectToRoute('bonreception_reglements', array('id' => $bonreception->getId()));
        }
        $ligneReglements = $em->getRepository('App\\Entity\\LigneReglementBonReception')->findByBonReception($bonreception);
        return $this->render('bonreception/reglements.html.twig', array(
                    'bonreception' => $bonreception,
                    'ligneReglements' => $ligneReglements,
                    'ligneReglement' => $ligneReglement,
                    'form' => $form->createView(),
        ));
    }

    /**
     * delete reglement
     * 
     * @Route("/{id}/reglement/{idLigneReglement}/delete",name="bonreception_reglement_delete", methods={"GET"})
     */
    public function reglementDeleteAction($id, $idLigneReglement) {
        $em = $this->getEm();
        $ligneReglement = $em->getRepository('App\\Entity\\LigneReglementBonReception')->find($idLigneReglement);
        $em->remove($ligneReglement);
        $em->flush($ligneReglement);
        return $this->redirectToRoute('bonreception_reglements', array('id' => $id));
    }

}

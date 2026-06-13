<?php

namespace App\Controller;

use App\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneReglement;
use Numbers_Words;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Facture controller.
 *
 * @Route("facture")
 */
class FactureController extends Controller {
    
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
        if ($request->query->get('startDateEcheance')) {
            $startDateEcheance = $dateFormat->formatDate($request->query->get('startDateEcheance'));
            if ($startDateEcheance) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date echéance >= ' . str_replace('/', '-', $request->query->get('startDateEcheance'));
            }
        }
        if ($request->query->get('endDateEcheance')) {
            $endDateEcheance = $dateFormat->formatDate($request->query->get('endDateEcheance'));
            if ($endDateEcheance) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date echéance <= ' . str_replace('/', '-', $request->query->get('endDateEcheance'));
            }
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\Facture')->createQueryBuilder('a');
        $qb->join('a.client', 'clt');
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
        $startDateEcheance = null;
        if ($request->query->get('startDateEcheance')) {
            $startDateEcheance = $dateFormat->formatDate($request->query->get('startDateEcheance'));
            if ($startDateEcheance) {
                $qb->andWhere('a.dateEcheance >= :startDateEcheance')->setParameter('startDateEcheance', $startDateEcheance);
            }
        }
        $endDateEcheance = null;
        if ($request->query->get('endDateEcheance')) {
            $endDateEcheance = $dateFormat->formatDate($request->query->get('endDateEcheance'));
            if ($endDateEcheance) {
                $qb->andWhere('a.dateEcheance <= :endDateEcheance')->setParameter('endDateEcheance', $endDateEcheance);
            }
        }
        return $qb;
    }

    /**
     * Lists all facture entities.
     *
     * @Route("/", name="facture_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $qb = $this->getQbByParametres($em, $request);

        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $clients = $em->getRepository('App\\Entity\\Client')->findAll();
        return $this->render('facture/index.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients
        ));
    }
    
    /**
     * Totaux facture.
     *
     * @Route("/totaux/{code}/{client}/{termine}/{regle}/{startDateCreation}/{endDateCreation}/{startDateEcheance}/{endDateEcheance}", name="facture_totaux", methods={"GET"})
     */
    public function totauxFactureAction(Request $request,$code = null, $client = null, $termine = null,$regle = null, $startDateCreation = null, $endDateCreation = null, $startDateEcheance = null, $endDateEcheance = null) {
        $request->query->set('code', $code);
        $request->query->set('client', $client);
        $request->query->set('termine', $termine);
        $request->query->set('regle', $regle);
        $request->query->set('startDateCreation', $startDateCreation);
        $request->query->set('endDateCreation', $endDateCreation);
        $request->query->set('startDateEcheance', $startDateEcheance);
        $request->query->set('endDateEcheance', $endDateEcheance);
        $em= $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request)
                ->select('sum(a.total) as total')
                ->addSelect('sum(a.regle) as regle')
                ->getQuery()
                ->getScalarResult();
        $total=$qb[0]['total'];
        $regleVal=$qb[0]['regle'];
        return $this->render('_partial/totaux.html.twig', array(
                    'total' => $total,
                    'regle' => $regleVal
        ));
    }
    
    /**
     * Lists all facture entities print.
     *
     * @Route("/factureglobal", name="factureglobal", methods={"GET"})
     */
    public function factureGlobalAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);
        $client = null;
        if ($request->query->get('client')) {
            $client = $em->getRepository('App\\Entity\\Client')->findOneById($request->query->get('client'));
        }
        return $this->render('facture/factureglobal.html.twig', array(
                    'factures' => $qb->getQuery()->getResult(),
                    'societe' => $societe,
                    'client' => $client,
                    'code' => $request->query->get('client'),
                    'startDateCreation' => $request->query->get('startDateCreation'),
                    'endDateCreation' => $request->query->get('endDateCreation'),
                    'startDateEcheance' => $request->query->get('startDateEcheance'),
                    'endDateEcheance' => $request->query->get('endDateEcheance')
        ));
    }
    
    /**
     * Lists all stocks entities.
     *
     * @Route("/export/xls", name="facture_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'facture' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des factures ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Code')
                ->setCellValue('B4','Passager/Client')
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
                ->setCellValue('M4', 'Total avoir remboursé')
                ->setCellValue('N4', 'Total avoir non remboursé')
                ->setCellValue('O4', 'Total bénifice')
                ->setCellValue('P4', 'Terminé')
                ->setCellValue('Q4', 'Date création')
                ->setCellValue('R4', 'Date echéance')
                ->setCellValue('S4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A' . $i, $entity->getCode())
                    ->setCellValue('B'.$i,$entity->getClient()->getPassager()?'Passager':'Client')
                    ->setCellValue('C' . $i, $entity->getClient()->getCode())
                    ->setCellValue('D' . $i, $entity->getClient()->getRs())
                    ->setCellValue('E' . $i, $entity->getNom())
                    ->setCellValue('F' . $i, $entity->getCin())
                    ->setCellValue('G' . $i, $entity->getHt())
                    ->setCellValue('H' . $i, $entity->getRemise())
                    ->setCellValue('I' . $i, $entity->getTva())
                    ->setCellValue('J' . $i, $entity->getTotal())
                    ->setCellValue('K' . $i, $entity->getRegle())
                    ->setCellValue('L' . $i, $entity->getReste()<0?'0':$entity->getReste())
                    ->setCellValue('M' . $i, $entity->getTotalAvoirRembourse())
                    ->setCellValue('N' . $i, $entity->getTotalAvoirNonRembourse())
                    ->setCellValue('O' . $i, $entity->getBenifice())
                    ->setCellValue('P' . $i, $entity->getTermine()?'Terminé':'En cours')
                    ->setCellValue('Q' . $i, $entity->getDateCreation()?$entity->getDateCreation()->format('d-m-Y'):'')
                    ->setCellValue('R' . $i, $entity->getDateEcheance()?$entity->getDateEcheance()->format('d-m-Y'):'')
                    ->setCellValue('S' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Factures');
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
     * Creates a new facture entity.
     *
     * @Route("/new", name="facture_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        $facture = new Facture();
        $form = $this->createForm('App\Form\FactureType', $facture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($facture);
            $em->flush($facture);
            if ($form->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('facture_print', array('id' => $facture->getId()));
            }
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        return $this->render('facture/new.html.twig', array(
                    'facture' => $facture,
                    'form' => $form->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * @Route("/{id}/show",name="facture_show", methods={"GET","POST"})
     */
    public function showAction(Request $request, facture $facture) {
        $form_regler = $this->createFormBuilder($facture)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndRegler', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la facture et régler', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_imprimer = $this->createFormBuilder($facture)
                ->add('termine', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class, array(
                    'data' => true
                ))
                ->add('terminerAndImprimer', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array('label' => 'Terminer la facture et imprimer', 'attr' => ['class' => 'btn-success']))
                ->getForm();
        $form_regler->handleRequest($request);
        $form_imprimer->handleRequest($request);
        if ($form_regler->isSubmitted() && $form_regler->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('facture_reglements', array('id' => $facture->getId()));
        }
        if ($form_imprimer->isSubmitted() && $form_imprimer->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('facture_print', array('id' => $facture->getId()));
        }
        return $this->render('facture/show.html.twig', array(
                    'facture' => $facture,
                    'form_regler' => $form_regler->createView(),
                    'form_imprimer' => $form_imprimer->createView()
        ));
    }

    /**
     * Displays a form to edit an existing facture entity.
     *
     * @Route("/{id}/edit", name="facture_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Facture $facture) {
        $em = $this->getDoctrine()->getManager();
        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();
        if ($facture->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Cette facture est terminé, on ne peut pas la modifier.');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        $originalLignesFactures = new ArrayCollection(); // Create an ArrayCollection of the current Tag objects in the database
        foreach ($facture->getLignesFactures() as $ligne) {
            $originalLignesFactures->add($ligne);
        }
        $editForm = $this->createForm('App\Form\FactureType', $facture, array(
            'disabled' => $facture->getRegle() > 0
        ));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            foreach ($originalLignesFactures as $ligne) {// remove the relationship between the tag and the Task
                if (false === $facture->getLignesFactures()->contains($ligne)) {
                    $ligne->setFacture(null);
                    $em->persist($ligne);
                    $em->remove($ligne);
                }
            }
            $em->flush();


            if ($editForm->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('facture_print', array('id' => $facture->getId()));
            }
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }

        return $this->render('facture/edit.html.twig', array(
                    'facture' => $facture,
                    'edit_form' => $editForm->createView(),
                    'retenus' => $retenus
        ));
    }

    /**
     * Displays a form to print an existing devi entity.
     *
     * @Route("/{id}/print", name="facture_print", methods={"GET"})
     */
    public function printAction(Facture $facture, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);

        $totalDinars = intval($facture->getTotal());
        $totalMillimesEnTtLettres = explode('.', number_format($facture->getTotal() - intval($facture->getTotal()), 3))[1];
        $totalDinarsEnTtLettres = Numbers_Words::toWords($totalDinars, $request->getLocale());
        return $this->render('facture/print.html.twig', array(
                    'facture' => $facture,
                    'societe' => $societe,
                    'totalDinarsEnTtLettres' => $totalDinarsEnTtLettres,
                    'totalMillimesEnTtLettres' => $totalMillimesEnTtLettres
        ));
    }

    /**
     * Reglements
     * 
     * @Route("/{id}/reglements",name="facture_reglements", methods={"GET","POST"})
     */
    public function reglementsAction(Request $request, Facture $facture) {
        if (!$facture->getTermine()) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut terminer la facture pour faire un réglement');
            return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
        }
        $em = $this->getDoctrine()->getManager();
        $ligneReglement = new LigneReglement();
        $ligneReglement->setFacture($facture);
        $ligneReglement->setType('reglement');

        $form = $this->createForm('App\Form\LigneReglementType', $ligneReglement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ligneReglement);
            $em->flush($ligneReglement);
            return $this->redirectToRoute('facture_reglements', array('id' => $facture->getId()));
        }
        $totalAvoir = $em->getRepository('App\\Entity\\Facture')->findAllTotalAvoirRemboursement($facture->getId());
        //$totalAvoir=0;
        $ligneReglements = $em->getRepository('App\\Entity\\LigneReglement')->findByFacture($facture);
        return $this->render('facture/reglements.html.twig', array(
                    'facture' => $facture,
                    'ligneReglements' => $ligneReglements,
                    'ligneReglement' => $ligneReglement,
                    'form' => $form->createView(),
                    'totalAvoir' => $totalAvoir
        ));
    }

    /**
     * delete reglement
     * 
     * @Route("/{id}/reglement/{idLigneReglement}/delete",name="facture_reglement_delete", methods={"GET"})
     */
    public function reglementDeleteAction($id, $idLigneReglement) {
        $em = $this->getDoctrine()->getManager();
        $ligneReglement = $em->getRepository('App\\Entity\\LigneReglement')->find($idLigneReglement);
        $em->remove($ligneReglement);
        $em->flush($ligneReglement);
        return $this->redirectToRoute('facture_reglements', array('id' => $id));
    }

    //api

    /**
     * Get facture
     *
     * @Route("/api", name="facture", methods={"GET"})
     */
    public function getFactureAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $facture = $em->getRepository('App\\Entity\\Facture')->findOneById($id);
        $response = array();
        if (!$facture) {
            $response = array(
                'success' => false,
                'content' => 'Facture non trouvé'
            );
        } else {
            $serializer = $this->container->get('serializer');
            $response = array(
                'success' => true,
                'facture' => $serializer->serialize($facture, 'json', array('groups' => array('facture')))
            );
        }
        return new JsonResponse($response);
    }

}

<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Mouvement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Mouvement controller.
 *
 * @Route("mouvement")
 */
class MouvementController extends Controller {

    public function getTitreByParameteres($em, $request) {
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
        if ($request->query->get('mouvement')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Mouvement : ' . $request->query->get('mouvement');
        }
        if ($request->query->get('client')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $client = $em->getRepository('AppBundle:Client')->findOneById($request->query->get('client'));
            $chaine .= 'Code client : ' . $client->getCode();
        }
        if ($request->query->get('fournisseur')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $fournisseur = $em->getRepository('AppBundle:Fournisseur')->findOneById($request->query->get('fournisseur'));
            $chaine .= 'Code fournisseur : ' . $fournisseur->getCode();
        }
        if ($request->query->get('etat')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Etat : ' . $request->query->get('etat');
        }
        if ($request->query->get('modeReglement') and $request->query->get('modeReglement') !== 'tous') {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Mode de réglement : ' . $request->query->get('modeReglement');
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
        $qb = $em->getRepository('AppBundle:Mouvement')->createQueryBuilder('a');
        $mvt_designation = null;
        if ($request->query->get('designation')) {
            $mvt_designation = $request->query->get('designation');
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $mvt_designation . '%');
        }
        $mvt_typeDoc = null;
        if ($request->query->get('typeDoc')) {
            $mvt_typeDoc = $request->query->get('typeDoc');
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $mvt_typeDoc);
        }
        $mvt_mouvement = null;
        $mvt_client = null;
        $mvt_fournisseur = null;
        if ($request->query->get('mouvement')) {
            $mvt_mouvement = $request->query->get('mouvement');
            if ($mvt_mouvement === 'revenu') {
                $qb->andWhere("a.mouvement = 'revenu'");
                if ($request->query->get('client')) {
                    $mvt_client = $request->query->get('client');
                    $qb->andWhere('a.client = :client')->setParameter('client', $mvt_client);
                }
            } else if ($mvt_mouvement === 'depense') {
                $qb->andWhere("a.mouvement = 'depense'");
                if ($request->query->get('fournisseur')) {
                    $mvt_fournisseur = $request->query->get('fournisseur');
                    $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $mvt_fournisseur);
                }
            }
        }
        if ($request->query->get('etat')) {
            $etats = $request->query->get('etat');
            $chaine = "(";
            for ($i = 0; $i < count($etats); $i++) {
                $chaine .= "'" . $etats[$i] . "'";
                if (count($etats) - $i > 1) {
                    $chaine .= ",";
                }
            }
            $chaine .= ")";
            $qb->andWhere('a.etat IN ' . $chaine);
        }


        $mvt_startDateCreation = null;
        $dateFormat = $this->get('app.format_date'); //service for formatting date
        if ($request->query->get('startDateCreation')) {
            $mvt_startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            if ($mvt_startDateCreation) {
                $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $mvt_startDateCreation);
            }
        }
        $mvt_endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $mvt_endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            if ($mvt_endDateCreation) {
                $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $mvt_endDateCreation);
            }
        }
        $mvt_startDateEcheance = null;
        if ($request->query->get('startDateEcheance')) {
            $mvt_startDateEcheance = $dateFormat->formatDate($request->query->get('startDateEcheance'));
            if ($mvt_startDateEcheance) {
                $qb->andWhere('a.dateEcheance >= :startDateEcheance')->setParameter('startDateEcheance', $mvt_startDateEcheance);
            }
        }
        $mvt_endDateEcheance = null;
        if ($request->query->get('endDateEcheance')) {
            $mvt_endDateEcheance = $dateFormat->formatDate($request->query->get('endDateEcheance'));
            if ($mvt_endDateEcheance) {
                $qb->andWhere('a.dateEcheance <= :endDateEcheance')->setParameter('endDateEcheance', $mvt_endDateEcheance);
            }
        }
        $mvt_modeReglement = null;
        if ($request->query->get('modeReglement') and $request->query->get('modeReglement') !== 'tous') {
            $mvt_modeReglement = $request->query->get('modeReglement');
            $qb->andWhere('a.modeReglement like :modeReglement')->setParameter('modeReglement', $mvt_modeReglement);
        }
        return $qb;
    }

    /**
     * Lists all mouvement entities.
     *
     * @Route("/", name="mouvement_index")
     * @Method("GET")
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
        $clients = $em->getRepository('AppBundle:Client')->findAll();
        $fournisseurs = $em->getRepository('AppBundle:Fournisseur')->findAll();

        $total = $qb->select('SUM(a.ttc) as sumDepenses')
                ->getQuery()
                ->getSingleScalarResult();
        $sumRevenus = $qb->andWhere("a.mouvement = 'revenu'")
                ->select('SUM(a.ttc) as sumRevenus')
                ->getQuery()
                ->getSingleScalarResult();


        $sumDepenses = $sumRevenus - $total;
        return $this->render('mouvement/index.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients,
                    'fournisseurs' => $fournisseurs,
                    'sumRevenus' => $sumRevenus,
                    'sumDepenses' => - $sumDepenses,
                    'total' => $sumRevenus + $sumDepenses
        ));
    }

    /**
     * Lists all mouvements entities.
     *
     * @Route("/export/xls", name="mouvement_export_xls")
     * @Method("GET")
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'mouvements' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des mouvements ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Désignation')
                ->setCellValue('B4', 'Mouvement')
                ->setCellValue('C4', 'Type document')
                ->setCellValue('D4', 'Tier')
                ->setCellValue('E4', 'Code tier')
                ->setCellValue('F4', 'Raison social tier')
                ->setCellValue('G4', 'Total TTC')
                ->setCellValue('H4', 'Mode de réglement')
                ->setCellValue('I4', 'Numéro document')
                ->setCellValue('J4', 'Compte recevant')
                ->setCellValue('K4', 'Date création')
                ->setCellValue('L4', 'Date echéance')
                ->setCellValue('M4', 'Etat')
                ->setCellValue('N4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $codeTier = '';
            $rsTier = '';
            if ($entity->getFournisseur()) {
                $codeTier = $entity->getFournisseur()->getCode();
                $rsTier = $entity->getFournisseur()->getCode();
            } elseif ($entity->getClient()) {
                $codeTier = $entity->getClient()->getCode();
                $rsTier = $entity->getClient()->getCode();
            }
            $sheet->setCellValue('A' . $i, $entity->getDesignation())
                    ->setCellValue('B' . $i, $entity->getMouvement())
                    ->setCellValue('C' . $i, $entity->getTypeDoc())
                    ->setCellValue('D' . $i, $entity->getFournisseur() ? 'Fournisseur' : 'Client')
                    ->setCellValue('E' . $i, $codeTier)
                    ->setCellValue('F' . $i, $rsTier)
                    ->setCellValue('G' . $i, $entity->getTtc())
                    ->setCellValue('H' . $i, $entity->getModeReglement())
                    ->setCellValue('I' . $i, $entity->getNumDoc())
                    ->setCellValue('J' . $i, $entity->getCompte())
                    ->setCellValue('K' . $i, $entity->getDateCreation() ? $entity->getDateCreation()->format('d-m-Y') : '')
                    ->setCellValue('L' . $i, $entity->getDateEcheance() ? $entity->getDateEcheance()->format('d-m-Y') : '')
                    ->setCellValue('M' . $i, $entity->getEtat())
                    ->setCellValue('N' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Mouvements');
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
     * Creates a new mouvement entity.
     *
     * @Route("/depense/new", name="depense_new")
     * @Method({"GET", "POST"})
     */
    public function depenseNewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $retenus = $em->getRepository('AppBundle:Retenu')->findAll();
        $mouvement = new Mouvement();
        $mouvement->setMouvement('depense');
        $form = $this->createForm('AppBundle\Form\MouvementType', $mouvement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($mouvement);
            $em->flush($mouvement);
            return $this->redirectToRoute('mouvement_index');
        }

        return $this->render('mouvement/depense_new.html.twig', array(
                    'mouvement' => $mouvement,
                    'retenus' => $retenus,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new mouvement entity.
     *
     * @Route("/revenu/new", name="revenu_new")
     * @Method({"GET", "POST"})
     */
    public function revenuNewAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $retenus = $em->getRepository('AppBundle:Retenu')->findAll();
        $mouvement = new Mouvement();
        $mouvement->setMouvement('revenu');
        $form = $this->createForm('AppBundle\Form\MouvementType', $mouvement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mouvement);
            $em->flush($mouvement);
            return $this->redirectToRoute('mouvement_index');
        }
        return $this->render('mouvement/revenu_new.html.twig', array(
                    'mouvement' => $mouvement,
                    'retenus' => $retenus,
                    'form' => $form->createView(),
        ));
    }

}

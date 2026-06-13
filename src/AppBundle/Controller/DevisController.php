<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Devis;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Facture;
use AppBundle\Entity\LigneFacture;
use Numbers_Words;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Devi controller.
 *
 * @Route("devis")
 */
class DevisController extends Controller {
    
    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('client')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $client = $em->getRepository('AppBundle:Client')->findOneById($request->query->get('client'));
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
        $qb = $em->getRepository('AppBundle:Devis')->createQueryBuilder('a');
        $qb->join('a.client', 'clt');
        //filter
        if ($request->query->get('code')) {
            $qb->where('a.code like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        if ($request->query->get('client')) {
            $qb->andWhere('a.client = :client')->setParameter('client', $request->query->get('client'));
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
        if ($request->query->get('startDateValidite')) {
            $startDateValidite = $dateFormat->formatDate($request->query->get('startDateValidite'));
            if ($startDateValidite) {
                $qb->andWhere('a.dateValidite >= :startDateValidite')->setParameter('startDateValidite', $startDateValidite);
            }
        }
        if ($request->query->get('endDateValidite')) {
            $endDateValidite = $dateFormat->formatDate($request->query->get('endDateValidite'));
            if ($endDateValidite) {
                $qb->andWhere('a.dateValidite <= :endDateValidite')->setParameter('endDateValidite', $endDateValidite);
            }
        }
        return $qb;
    }

    /**
     * Lists all devi entities.
     *
     * @Route("/", name="devis_index", methods={"GET"})
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
        return $this->render('devis/index.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients,
        ));
    }
    
    /**
     * Lists all devis entities.
     *
     * @Route("/export/xls", name="devis_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'devis' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        
        $sheet->setCellValue('A1', 'Liste des devis ( ' . count($entities) . ' résultat(s) )');
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
                ->setCellValue('K4', 'Terminé')
                ->setCellValue('L4', 'Date création')
                ->setCellValue('M4', 'Date validité')
                ->setCellValue('N4', 'Note');
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
                    ->setCellValue('K' . $i, $entity->getTermine()?'Terminé':'En cours')
                    ->setCellValue('L' . $i, $entity->getDateCreation()?$entity->getDateCreation()->format('d-m-Y'):'')
                    ->setCellValue('M' . $i, $entity->getDateValidite()?$entity->getDateValidite()->format('d-m-Y'):'')
                    ->setCellValue('N' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Liste des devis');
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
     * Creates a new devi entity.
     *
     * @Route("/new", name="devis_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $devi = new Devis();


        $form = $this->createForm('AppBundle\Form\DevisType', $devi);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($devi);
            $em->flush($devi);

            if ($form->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('devis_print', array('id' => $devi->getId()));
            }
            return $this->redirectToRoute('devis_edit', array('id' => $devi->getId()));
        }

        return $this->render('devis/new.html.twig', array(
                    'devi' => $devi,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Convert devis to facture
     * @Route("/{id}/facture",name="devis_to_facture", methods={"GET"})
     */
    public function devisToFacture(Devis $devi) {
        $em = $this->getDoctrine()->getManager();
        $facture = new Facture();
        $facture->setClient($devi->getClient());
        $facture->setHt($devi->getHt());
        $facture->setNote($devi->getNote());
        $facture->setRemise($devi->getRemise());
        $facture->setTermine(false);
        $facture->setTotal($devi->getTotal() + 0.5);
        $facture->setTva($devi->getTva());
        $em->persist($facture);
        $em->flush($facture);
        foreach ($devi->getLignesDevis() as $ligne) {
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
        return $this->redirectToRoute('facture_show', array('id' => $facture->getId()));
    }

    /**
     * @Route("/{id}/show",name="devis_show", methods={"GET"})
     */
    public function showAction(Devis $devi) {
        return $this->render('devis/show.html.twig', array(
                    'devi' => $devi,
        ));
    }

    /**
     * Displays a form to edit an existing devi entity.
     *
     * @Route("/{id}/edit", name="devis_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Devis $devi) {
        $originalLignesDevis = new ArrayCollection();
        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($devi->getLignesDevis() as $ligne) {
            $originalLignesDevis->add($ligne);
        }
        $editForm = $this->createForm('AppBundle\Form\DevisType', $devi);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            // remove the relationship between the tag and the Task
            foreach ($originalLignesDevis as $ligne) {
                if (false === $devi->getLignesDevis()->contains($ligne)) {
                    $ligne->setDevis(null);
                    $em->persist($ligne);
                    $em->remove($ligne);
                }
            }
            $em->flush();
            if ($editForm->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('devis_print', array('id' => $devi->getId()));
            }
            return $this->redirectToRoute('devis_show', array('id' => $devi->getId()));
        }

        return $this->render('devis/edit.html.twig', array(
                    'devi' => $devi,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Displays a form to print an existing devi entity.
     *
     * @Route("/{id}/print", name="devis_print", methods={"GET"})
     */
    public function printAction(Devis $devi,Request $request) {
        $em = $this->getDoctrine()->getManager();
        $societe = $em->getRepository('AppBundle:Societe')->find(1);
        
        $totalDinars = intval($devi->getTotal());
        $totalMillimesEnTtLettres=explode('.',number_format($devi->getTotal()-intval($devi->getTotal()),3) )[1];
        $totalDinarsEnTtLettres = Numbers_Words::toWords($totalDinars, $request->getLocale());
        
        return $this->render('devis/print.html.twig', array(
                    'devis' => $devi,
                    'societe' => $societe,
                    'totalDinarsEnTtLettres'=>$totalDinarsEnTtLettres,
            'totalMillimesEnTtLettres'=>$totalMillimesEnTtLettres
        ));
    }

    /**
     * Deletes a devi entity.
     *
     * @Route("/{id}", name="devis_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Devis $devi) {
        $form = $this->createDeleteForm($devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($devi);
            $em->flush($devi);
        }

        return $this->redirectToRoute('devis_index');
    }

    /**
     * Creates a form to delete a devi entity.
     *
     * @param Devis $devi The devi entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Devis $devi) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('devis_delete', array('id' => $devi->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

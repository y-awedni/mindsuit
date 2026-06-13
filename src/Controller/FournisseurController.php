<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Fournisseur controller.
 *
 * @Route("fournisseur")
 */
class FournisseurController extends BaseController {
    
    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('rs')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Raison sociale contient ' . $request->query->get('rs');
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\Fournisseur')->createQueryBuilder('a');
        //filter
        if ($request->query->get('code')) {
            $qb->where('a.code like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        if ($request->query->get('rs')) {
            $qb->andWhere('a.rs like :rs')->setParameter('rs', '%' . $request->query->get('rs') . '%');
        }
        return $qb;
    }
    
    /**
     * Lists all fournisseur entities.
     *
     * @Route("/", name="fournisseur_index", methods={"GET"})
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
        return $this->render('fournisseur/index.html.twig', array(
                    'pagination' => $pagination,
        ));
    }
    
    /**
     * Lists all fournisseur entities.
     *
     * @Route("/export/xls", name="fournisseur_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'fournisseurs' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des fournisseurs ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Code')
                ->setCellValue('B4', 'RS')
                ->setCellValue('C4', 'Matricule fiscale')
                ->setCellValue('D4', 'Adresse4')
                ->setCellValue('E4', 'Adresse2')
                ->setCellValue('F4', 'Adresse3')
                ->setCellValue('G4', 'Pays')
                ->setCellValue('H4', 'Ville')
                ->setCellValue('I4', 'Code postal')
                ->setCellValue('J4', 'Tel')
                ->setCellValue('K4', 'Mobile')
                ->setCellValue('L4', 'Fax')
                ->setCellValue('M4', 'Email')
                ->setCellValue('N4', 'Site web')
                ->setCellValue('O4', 'Note');
        $i = 5;
        foreach ($entities as $fournisseur) {
            $sheet->setCellValue('A' . $i, $fournisseur->getCode())
                ->setCellValue('B' . $i, $fournisseur->getRs())
                ->setCellValue('C' . $i, $fournisseur->getMf())
                ->setCellValue('D' . $i, $fournisseur->getAdresse1())
                ->setCellValue('E' . $i, $fournisseur->getAdresse2())
                ->setCellValue('F' . $i, $fournisseur->getAdresse3())
                ->setCellValue('G' . $i, $fournisseur->getPays())
                ->setCellValue('H' . $i, $fournisseur->getVille())
                ->setCellValue('I' . $i, $fournisseur->getCodePostal())
                ->setCellValue('J' . $i, $fournisseur->getTel())
                ->setCellValue('K' . $i, $fournisseur->getMobile())
                ->setCellValue('L' . $i, $fournisseur->getFax())
                ->setCellValue('M' . $i, $fournisseur->getEmail())
                ->setCellValue('N' . $i, $fournisseur->getSiteWeb())
                ->setCellValue('O' . $i, $fournisseur->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Fournisseurs');
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
     * Displays a form to show an existing fournisseur entity.
     *
     * @Route("/{id}/show", name="fournisseur_show", methods={"GET"})
     */
    public function showAction(Fournisseur $fournisseur) {
        return $this->render('fournisseur/show.html.twig', array(
                    'fournisseur' => $fournisseur,
        ));
    }

    /**
     * Creates a new fournisseur entity.
     *
     * @Route("/new", name="fournisseur_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $fournisseur = new Fournisseur();
        $form = $this->createForm('App\Form\FournisseurType', $fournisseur);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($fournisseur);
            $em->flush($fournisseur);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('fournisseur_new');
            }
            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
        }

        return $this->render('fournisseur/new.html.twig', array(
                    'fournisseur' => $fournisseur,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing fournisseur entity.
     *
     * @Route("/{id}/edit", name="fournisseur_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Fournisseur $fournisseur) {
        $editForm = $this->createForm('App\Form\FournisseurType', $fournisseur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('fournisseur_edit', array('id' => $fournisseur->getId()));
        }

        return $this->render('fournisseur/edit.html.twig', array(
                    'fournisseur' => $fournisseur,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a fournisseur entity.
     *
     * @Route("/{id}/delete", name="fournisseur_delete", methods={"GET"})
     */
    public function deleteAction(Fournisseur $fournisseur) {
        if ($fournisseur) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($fournisseur);
                $em->flush($fournisseur);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('fournisseur_index');
    }
    
    /**
     * Creates a new custom fournisseur entity.
     *
     * @Route("/custom/new", name="fournisseur_custom_new", methods={"GET", "POST"})
     */
    public function customNewAction(Request $request) {
        $fournisseur = new Fournisseur();
        $form = $this->createForm('App\Form\Custom\FournisseurType', $fournisseur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($fournisseur);
            $em->flush($fournisseur);
            //a redefinir
            return new JsonResponse(array(
                        'success' =>true,
                        'fournisseurId'=>$fournisseur->getId(),
                        'toString'=>"Code : ". $fournisseur->getCode()." : Rs: ".$fournisseur->getRs()
                    ));
        }
        return $this->render('fournisseur/custom/new.html.twig', array(
                    'fournisseur' => $fournisseur,
                    'form' => $form->createView(),
        ));
    }
}

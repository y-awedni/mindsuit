<?php

namespace App\Controller;

use App\Entity\BonCommandeFrs;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Boncommandefr controller.
 *
 * @Route("boncommandefrs")
 */
class BonCommandeFrsController extends Controller {
    
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
        if ($request->query->get('startDateCommande')) {
            $startDateCommande = $dateFormat->formatDate($request->query->get('startDateCommande'));
            if ($startDateCommande) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date commande >= ' . str_replace('/', '-', $request->query->get('startDateCommande'));
            }
        }
        if ($request->query->get('endDateCommande')) {
            $endDateCommande = $dateFormat->formatDate($request->query->get('endDateCommande'));
            if ($endDateCommande) {
                $i > 0 ? $chaine .= ',' : $i++;
                $chaine .= 'Date commande <= ' . str_replace('/', '-', $request->query->get('endDateCommande'));
            }
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('App\\Entity\\BonCommandeFrs')->createQueryBuilder('a');
        $qb->join('a.fournisseur', 'frs');
        //filter
        $code = null;
        if ($request->query->get('code')) {
            $code = $request->query->get('code');
            $qb->where('a.code like :code')->setParameter('code', '%' . $code . '%');
        }
        $fournisseur = null;
        if ($request->query->get('fournisseur')) {
            $fournisseur = $request->query->get('fournisseur');
            $qb->andWhere('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);
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
        $startDateCommande = null;
        if ($request->query->get('startDateCommande')) {
            $startDateCommande = $dateFormat->formatDate($request->query->get('startDateCommande'));
            if ($startDateCommande) {
                $qb->andWhere('a.dateCommande >= :startDateCommande')->setParameter('startDateCommande', $startDateCommande);
            }
        }
        $endDateCommande = null;
        if ($request->query->get('endDateCommande')) {
            $endDateCommande = $dateFormat->formatDate($request->query->get('endDateCommande'));
            if ($endDateCommande) {
                $qb->andWhere('a.dateCommande <= :endDateCommande')->setParameter('endDateCommande', $endDateCommande);
            }
        }
        return $qb;
    }

    /**
     * Lists all bonCommandeFrs entities.
     *
     * @Route("/", name="boncommandefrs_index", methods={"GET"})
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
        $fournisseurs = $em->getRepository('App\\Entity\\Fournisseur')->findAll();
        return $this->render('boncommandefrs/index.html.twig', array(
                    'pagination' => $pagination,
                    'fournisseurs' => $fournisseurs
        ));
    }
    
    /**
     * Lists all article entities.
     *
     * @Route("/export/xls", name="boncommandefrs_export_xls", methods={"GET"})
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'boncommandefrs' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des bon de commandes fournisseur ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Code')
                ->setCellValue('B4', 'Code fournisseur')
                ->setCellValue('C4', 'Raison social fournisseur')
                ->setCellValue('D4', 'Date création')
                ->setCellValue('E4', 'Date commande')
                ->setCellValue('F4', 'Terminé')
                ->setCellValue('G4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A' . $i, $entity->getCode())
                ->setCellValue('B' . $i, $entity->getFournisseur()->getCode())
                ->setCellValue('C' . $i, $entity->getFournisseur()->getRs())
                ->setCellValue('D' . $i, $entity->getDateCreation())
                ->setCellValue('E' . $i, $entity->getDateCommande())
                ->setCellValue('F' . $i, $entity->getTermine()?'Terminé':'En cours')
                ->setCellValue('G' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Bon de commande');
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
     * Creates a new bonCommandeFrs entity.
     *
     * @Route("/new", name="boncommandefrs_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $bonCommandeFrs = new Boncommandefrs();
        $form = $this->createForm('App\Form\BonCommandeFrsType', $bonCommandeFrs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bonCommandeFrs);
            $em->flush($bonCommandeFrs);

            return $this->redirectToRoute('boncommandefrs_show', array('id' => $bonCommandeFrs->getId()));
        }

        return $this->render('boncommandefrs/new.html.twig', array(
                    'bonCommandeFrs' => $bonCommandeFrs,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a bonCommandeFrs entity.
     *
     * @Route("/{id}", name="boncommandefrs_show", methods={"GET"})
     */
    public function showAction(BonCommandeFrs $bonCommandeFrs) {
        $deleteForm = $this->createDeleteForm($bonCommandeFrs);

        return $this->render('boncommandefrs/show.html.twig', array(
                    'bonCommandeFrs' => $bonCommandeFrs,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to print an existing devi entity.
     *
     * @Route("/{id}/print", name="boncommandefrs_print", methods={"GET"})
     */
    public function printAction(BonCommandeFrs $bonCommandeFrs) {
        $em = $this->getDoctrine()->getManager();
        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);
        return $this->render('boncommandefrs/print.html.twig', array(
                    'bonCommandeFrs' => $bonCommandeFrs,
                    'societe' => $societe
        ));
    }

    /**
     * Displays a form to edit an existing bonCommandeFrs entity.
     *
     * @Route("/{id}/edit", name="boncommandefrs_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, BonCommandeFrs $bonCommandeFrs) {
        $originalLigneBonCommandeFrss = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($bonCommandeFrs->getLigneBonCommandeFrss() as $ligne) {
            $originalLigneBonCommandeFrss->add($ligne);
        }
        $editForm = $this->createForm('App\Form\BonCommandeFrsType', $bonCommandeFrs);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            foreach ($originalLigneBonCommandeFrss as $ligne) {
                if (false === $bonCommandeFrs->getLigneBonCommandeFrss()->contains($ligne)) {
                    $ligne->setBonCommandeFrs(null);
                    $em->persist($ligne);
                    $em->remove($ligne);
                }
            }
            $em->flush();
            if ($editForm->get('saveAndPrint')->isClicked()) {
                return $this->redirectToRoute('boncommandefrs_print', array('id' => $bonCommandeFrs->getId()));
            }
            return $this->redirectToRoute('boncommandefrs_edit', array('id' => $bonCommandeFrs->getId()));
        }

        return $this->render('boncommandefrs/edit.html.twig', array(
                    'bonCommandeFrs' => $bonCommandeFrs,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a bonCommandeFrs entity.
     *
     * @Route("/{id}", name="boncommandefrs_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, BonCommandeFrs $bonCommandeFrs) {
        $form = $this->createDeleteForm($bonCommandeFrs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bonCommandeFrs);
            $em->flush($bonCommandeFrs);
        }

        return $this->redirectToRoute('boncommandefrs_index');
    }

    /**
     * Creates a form to delete a bonCommandeFrs entity.
     *
     * @param BonCommandeFrs $bonCommandeFrs The bonCommandeFrs entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(BonCommandeFrs $bonCommandeFrs) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('boncommandefrs_delete', array('id' => $bonCommandeFrs->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

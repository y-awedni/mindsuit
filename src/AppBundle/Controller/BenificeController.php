<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Facture;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Benifice controller.
 *
 * @Route("facture/benifice")
 */
class BenificeController extends Controller {

    /**
     * Lists all facture entities.
     *
     * @Route("/", name="facture_benifice_index")
     * @Method("GET")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:Facture')->createQueryBuilder('a');
        //filter
        $code = null;
        if ($request->query->get('code')) {
            $code = $request->query->get('code');
            $qb->where('a.code like :code')->setParameter('code', '%' . $code . '%');
        }
        $total = null;
        if ($request->query->get('total')) {
            $total = $request->query->get('total');
            $qb->andWhere('a.total like :total')->setParameter('total', '%' . $total . '%');
        }
        $client = null;
        if ($request->query->get('client')) {
            $client = $request->query->get('client');
            $qb->andWhere('a.client = :client')->setParameter('client', $client);
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
        //die('ici3 '.$qb);
        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();

        if ($request->get('print')) {
            $societe = $em->getRepository('AppBundle:Societe')->find(1);
            if ($client) {
                $client = $em->getRepository('AppBundle:Client')->findOneById($client);
            }
            return $this->render('facture/blglobal.html.twig', array(
                        'factures' => $query->getResult(),
                        'societe' => $societe,
                        'client' => $client,
                        'code' => $code,
                        'startDateCreation' => $startDateCreation,
                        'endDateCreation' => $endDateCreation,
                        'startDateEcheance' => $startDateEcheance,
                        'endDateEcheance' => $endDateEcheance
            ));
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );
        $clients = $em->getRepository('AppBundle:Client')->findAll();
        return $this->render('benifice/index.html.twig', array(
                    'pagination' => $pagination,
                    'clients' => $clients
        ));
    }

    /**
     * @Route("/{code}/{total}/{client}/{startDateCreation}/{endDateCreation}/{startDateEcheance}/{endDateEcheance}/benefice",name="facture_benefice_get")
     * @Method({"GET"})
     */
    public function getBeneficeAction($code = null, $total = null, $client = null, $startDateCreation = null, $endDateCreation = null, $startDateEcheance = null, $endDateEcheance = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('AppBundle:Facture')->createQueryBuilder('a');
        if ($code) {
            $qb->where('a.code like :code')->setParameter('code', '%' . $code . '%');
        }
        if ($total) {
            $qb->andWhere('a.total like :total')->setParameter('total', '%' . $total . '%');
        }
        if ($client) {
            $qb->andWhere('a.client = :client')->setParameter('client', $client);
        }
        $dateFormat = $this->get('app.format_date');
        if ($startDateCreation) {
            $startDateCreation = $dateFormat->formatDate($startDateCreation);
            $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $endDateCreation = $dateFormat->formatDate($endDateCreation);
            $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        if ($startDateEcheance) {
            $startDateEcheance = $dateFormat->formatDate($startDateEcheance);
            $qb->andWhere('a.dateEcheance >= :startDateEcheance')->setParameter('startDateEcheance', $startDateEcheance);
        }
        if ($endDateEcheance) {
            $endDateEcheance = $dateFormat->formatDate($endDateEcheance);
            $qb->andWhere('a.dateEcheance <= :endDateEcheance')->setParameter('endDateEcheance', $endDateEcheance);
        }
        $query = $qb->getQuery();
        $factures = $query->getResult();
        $benifice = 0;
        foreach ($factures as $elem) {
            $benifice += $elem->getBenifice();
        }
        return $this->render('benifice/benifice.html.twig', array(
                    'benifice' => $benifice
        ));
    }

    
    /**
     * @Route("/{id}/show",name="facture_benifice_show")
     * @Method({"GET","POST"})
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
        return $this->render('benifice/show.html.twig', array(
                    'facture' => $facture,
                    'form_regler' => $form_regler->createView(),
                    'form_imprimer' => $form_imprimer->createView()
        ));
    }
    
}

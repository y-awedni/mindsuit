<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Client controller.
 *
 * @Route("client")
 */
class ClientController extends Controller {

    public function getTitreByParameteres($em, $request) {
        $chaine = '(';
        $i = 0;
        if ($request->query->get('code')) {
            $chaine .= 'Code contient ' . $request->query->get('code');
            $i++;
        }
        if ($request->query->get('rs')) {
            $chaine .= 'Raison sociale contient ' . $request->query->get('rs');
            $i++;
        }
        if ($request->query->get('nom')) {
            $chaine .= 'Nom contient ' . $request->query->get('nom');
            $i++;
        }
        if ($request->query->get('prenom')) {
            $chaine .= 'Prenom contient ' . $request->query->get('prenom');
            $i++;
        }
        if ($request->query->get('status')) {
            $i > 0 ? $chaine .= ',' : $i++;
            $chaine .= 'Status : ' . $request->query->get('status');
        }
        $chaine .= ')';
        return $chaine;
    }

    public function getQbByParametres($em, $request) {
        $qb = $em->getRepository('AppBundle:Client')->createQueryBuilder('a');
        //filter
        if ($request->query->get('code')) {
            $qb->where('a.code like :code')->setParameter('code', '%' . $request->query->get('code') . '%');
        }
        if ($request->query->get('status')) {
            $qb->andWhere('a.status like :status')->setParameter('status', '%' . $request->query->get('status') . '%');
        }
        if ($request->query->get('rs')) {
            $qb->andWhere('a.rs like :rs')->setParameter('rs', '%' . $request->query->get('rs') . '%');
        }
        if ($request->query->get('nom')) {
            $qb->andWhere('a.nom like :nom')->setParameter('nom', '%' . $request->query->get('nom') . '%');
        }
        if ($request->query->get('prenom')) {
            $qb->andWhere('a.prenom like :prenom')->setParameter('prenom', '%' . $request->query->get('prenom') . '%');
        }
        return $qb;
    }

    /**
     * Lists all client entities.
     *
     * @Route("/", name="client_index")
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
        return $this->render('client/index.html.twig', array(
                    'pagination' => $pagination,
        ));
    }

    /**
     * Lists all client entities.
     *
     * @Route("/export/xls", name="client_export_xls")
     * @Method("GET")
     */
    public function exportXlsAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getQbByParametres($em, $request);
        $titre = $this->getTitreByParameteres($em, $request);
        $entities = $qb->getQuery()->getResult();
        $dateSys = new \DateTime();
        $filename = 'clients' . $dateSys->format('d-m-Y_H:i:s') . '.xls';
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        $sheet = $phpExcelObject->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Liste des clients ( ' . count($entities) . ' résultat(s) )');
        $sheet->setCellValue('A2', $titre === '()' ? 'Pas de filtrage' : 'Filtre ' . $titre);
        $sheet->setCellValue('A4', 'Code')
                ->setCellValue('B4', 'Raison sociale')
                ->setCellValue('C4', 'Matricule fiscale')
                ->setCellValue('D4', 'Nom')
                ->setCellValue('E4', 'Prenom')
                ->setCellValue('F4', 'Status')
                ->setCellValue('G4', 'Civilité')
                ->setCellValue('H4', 'Activité')
                ->setCellValue('I4', 'Adresse4')
                ->setCellValue('J4', 'Adresse2')
                ->setCellValue('K4', 'Adresse3')
                ->setCellValue('L4', 'Pays')
                ->setCellValue('M4', 'Ville')
                ->setCellValue('N4', 'Code postal')
                ->setCellValue('O4', 'Mobile')
                ->setCellValue('P4', 'Tel')
                ->setCellValue('Q4', 'Fax')
                ->setCellValue('R4', 'Email')
                ->setCellValue('S4', 'Site web')
                ->setCellValue('T4', 'Remise')
                ->setCellValue('U4', 'Total avoir remboursé')
                ->setCellValue('V4', 'Total avoir non remboursé')
                ->setCellValue('W4', 'Note');
        $i = 5;
        foreach ($entities as $entity) {
            $sheet->setCellValue('A' . $i, $entity->getCode())
                    ->setCellValue('B' . $i, $entity->getRs())
                    ->setCellValue('C' . $i, $entity->getMf())
                    ->setCellValue('D' . $i, $entity->getNom())
                    ->setCellValue('E' . $i, $entity->getPrenom())
                    ->setCellValue('F' . $i, $entity->getStatus())
                    ->setCellValue('G' . $i, $entity->getCivilite())
                    ->setCellValue('H' . $i, $entity->getActivite())
                    ->setCellValue('I' . $i, $entity->getAdresse1())
                    ->setCellValue('J' . $i, $entity->getAdresse2())
                    ->setCellValue('K' . $i, $entity->getAdresse3())
                    ->setCellValue('L' . $i, $entity->getPays())
                    ->setCellValue('M' . $i, $entity->getVille())
                    ->setCellValue('N' . $i, $entity->getCodePostal())
                    ->setCellValue('O' . $i, $entity->getMobile())
                    ->setCellValue('P' . $i, $entity->getTel())
                    ->setCellValue('Q' . $i, $entity->getFax())
                    ->setCellValue('R' . $i, $entity->getEmail())
                    ->setCellValue('S' . $i, $entity->getSiteWeb())
                    ->setCellValue('T' . $i, $entity->getRemise())
                    ->setCellValue('U' . $i, $entity->getTotalAvoirRembourse())
                    ->setCellValue('V' . $i, $entity->getTotalAvoirNonRembourse())
                    ->setCellValue('W' . $i, $entity->getNote());
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Clients');
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
     * Displays a form to show an existing article entity.
     *
     * @Route("/{id}/show", name="client_show")
     * @Method({"GET"})
     */
    public function showAction(Client $client) {
        return $this->render('client/show.html.twig', array(
                    'client' => $client,
        ));
    }

    /**
     * Creates a new client entity.
     *
     * @Route("/new", name="client_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $client = new Client();
        $form = $this->createForm('AppBundle\Form\ClientType', $client);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($client->getPassager()) {
                $clientPassager = $em->getRepository('AppBundle:Client')->findOneByPassager(true);
                if ($clientPassager) {
                    $form->get('passager')->addError(new FormError('Il y a déja un client passager'));
                    return $this->render('client/new.html.twig', array(
                                'client' => $client,
                                'form' => $form->createView(),
                    ));
                }
            }
            $em->persist($client);
            $em->flush($client);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('client_new');
            }
            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/new.html.twig', array(
                    'client' => $client,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}/edit", name="client_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Client $client) {
        $editForm = $this->createForm('AppBundle\Form\ClientType', $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($client->getPassager()) {
                $qb = $em->getRepository('AppBundle:Client')->createQueryBuilder('c');
                $qb->where('c.passager=true')->andWhere('c.id != ' . $client->getId());
                $clientPassager = $qb->getQuery()->getOneOrNullResult();
                if ($clientPassager) {
                    $editForm->get('passager')->addError(new FormError('Il y a déja un client passager'));
                    return $this->render('client/edit.html.twig', array(
                                'client' => $client,
                                'edit_form' => $editForm->createView()
                    ));
                }
            }
            $em->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/edit.html.twig', array(
                    'client' => $client,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}/delete", name="client_delete")
     * @Method("GET")
     */
    public function deleteAction(Client $client) {
        if ($client) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($client);
                $em->flush($client);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('client_index');
    }

    /**
     * Displays client in session
     *
     * @Route("/session", name="client_session")
     * @Method({"GET"})
     */
    public function clientSessionAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $id = $request->query->get('id');
        $session = $this->get('session');
        if (!$session->has('idClient')) {
            $session->set('idClient', $id);
            $myresponse = array(
                'success' => true
            );
            return new JsonResponse($myresponse);
        }
        $idClient = $session->get('idClient');
        if ($idClient === $id) {
            $myresponse = array(
                'success' => true
            );
            return new JsonResponse($myresponse);
        }
        $myresponse = array(
            'success' => false,
            'content' => 'Il faut sélectionner des bons de livraison d\'un seul client'
        );
        return new JsonResponse($myresponse);
    }

    //api

    /**
     * Get client
     *
     * @Route("/api", name="client")
     * 
     * @Method("GET")
     */
    public function getClientAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $client = $em->getRepository('AppBundle:Client')->findOneById($id);
        $response = array();
        if (!$client) {
            $response = array(
                'success' => false,
                'content' => 'Client non trouvé'
            );
        } else {
            if ($client->getPassager()) {
                $response = array(
                    'success' => true,
                    'passager' => true,
                    'remise' => $client->getRemise()
                );
            } else {
                $response = array(
                    'success' => true,
                    'passager' => false,
                    'remise' => $client->getRemise()
                );
            }
        }
        return new JsonResponse($response);
    }

    /**
     * Creates a new custom client entity.
     *
     * @Route("/custom/new", name="client_custom_new")
     * @Method({"GET", "POST"})
     */
    public function customNewAction(Request $request) {
        $client = new Client();
        $client->setStatus('Client');
        $form = $this->createForm('AppBundle\Form\Custom\ClientType', $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush($client);
            //a redefinir
            return new JsonResponse(array(
                        'success' =>true,
                        'clientId'=>$client->getId(),
                        'toString'=>"Code : ". $client->getCode()." : Rs: ".$client->getRs(),
                        'remise'=>$client->getRemise()
                    ));
        }
        return $this->render('client/custom/new.html.twig', array(
                    'client' => $client,
                    'form' => $form->createView(),
        ));
    }

}

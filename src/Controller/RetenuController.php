<?php

namespace App\Controller;

use App\Entity\Retenu;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Retenu controller.
 *
 * @Route("retenu")
 */
class RetenuController extends BaseController {

    /**
     * Lists all retenu entities.
     *
     * @Route("/", name="retenu_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getEm();

        $retenus = $em->getRepository('App\\Entity\\Retenu')->findAll();

        return $this->render('retenu/index.html.twig', array(
                    'retenus' => $retenus,
        ));
    }

    /**
     * Creates a new retenu entity.
     *
     * @Route("/new", name="retenu_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $retenu = new Retenu();
        $form = $this->createForm('App\Form\RetenuType', $retenu);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($retenu);
            $em->flush($retenu);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('retenu_new');
            }
            return $this->redirectToRoute('retenu_index');
        }

        return $this->render('retenu/new.html.twig', array(
                    'retenu' => $retenu,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a retenu entity.
     *
     * @Route("/{id}/show", name="retenu_show", methods={"GET"})
     */
    public function showAction(Retenu $retenu) {
        $deleteForm = $this->createDeleteForm($retenu);

        return $this->render('retenu/show.html.twig', array(
                    'retenu' => $retenu,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing retenu entity.
     *
     * @Route("/{id}/edit", name="retenu_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Retenu $retenu) {
        $editForm = $this->createForm('App\Form\RetenuType', $retenu);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

            return $this->redirectToRoute('retenu_index');
        }

        return $this->render('retenu/edit.html.twig', array(
                    'retenu' => $retenu,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a retenu entity.
     *
     * @Route("/{id}/delete", name="retenu_delete", methods={"GET"})
     */
    public function deleteAction(Retenu $retenu) {
        if ($retenu) {
            try {
                $em = $this->getEm();
                $em->remove($retenu);
                $em->flush($retenu);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('retenu_index');
    }
    
    /**
     * Get facture
     *
     * @Route("/api", name="retenu_montant", methods={"GET"})
     */
    public function getRetenuMontantAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getEm();
        $taux = $request->query->get('taux');
        $retenu = $em->getRepository('App\\Entity\\Retenu')->findOneByTaux($taux);
        
        $response = array();
        if (!$retenu) {
            $response = array(
                'success' => false,
                'content' => 'Retenu non trouvé'
            );
        } else {
            $response = array(
                'success' => true,
                'montant' => $retenu->getMontant()
            );
        }
        return new JsonResponse($response);
    }

}

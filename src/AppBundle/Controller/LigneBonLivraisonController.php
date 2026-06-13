<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LigneBonLivraison;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Lignebonlivraison controller.
 *
 * @Route("lignebonlivraison")
 */
class LigneBonLivraisonController extends Controller {

    /**
     * Lists all ligneBonLivraison entities.
     *
     * @Route("/", name="lignebonlivraison_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $ligneBonLivraisons = $em->getRepository('AppBundle:LigneBonLivraison')->findAll();

        return $this->render('lignebonlivraison/index.html.twig', array(
                    'ligneBonLivraisons' => $ligneBonLivraisons,
        ));
    }

    /**
     * Creates a new ligneBonLivraison entity.
     *
     * @Route("/new", name="lignebonlivraison_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $ligneBonLivraison = new Lignebonlivraison();
        $form = $this->createForm('AppBundle\Form\LigneBonLivraisonType', $ligneBonLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneBonLivraison);
            $em->flush($ligneBonLivraison);

            return $this->redirectToRoute('lignebonlivraison_show', array('id' => $ligneBonLivraison->getId()));
        }

        return $this->render('lignebonlivraison/new.html.twig', array(
                    'ligneBonLivraison' => $ligneBonLivraison,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneBonLivraison entity.
     *
     * @Route("/{id}", name="lignebonlivraison_show", methods={"GET"})
     */
    public function showAction(LigneBonLivraison $ligneBonLivraison) {
        $deleteForm = $this->createDeleteForm($ligneBonLivraison);

        return $this->render('lignebonlivraison/show.html.twig', array(
                    'ligneBonLivraison' => $ligneBonLivraison,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneBonLivraison entity.
     *
     * @Route("/{id}/edit", name="lignebonlivraison_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneBonLivraison $ligneBonLivraison) {
        $deleteForm = $this->createDeleteForm($ligneBonLivraison);
        $editForm = $this->createForm('AppBundle\Form\LigneBonLivraisonType', $ligneBonLivraison);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lignebonlivraison_edit', array('id' => $ligneBonLivraison->getId()));
        }

        return $this->render('lignebonlivraison/edit.html.twig', array(
                    'ligneBonLivraison' => $ligneBonLivraison,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneBonLivraison entity.
     *
     * @Route("/{id}", name="lignebonlivraison_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneBonLivraison $ligneBonLivraison) {
        $form = $this->createDeleteForm($ligneBonLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneBonLivraison);
            $em->flush($ligneBonLivraison);
        }

        return $this->redirectToRoute('lignebonlivraison_index');
    }

    /**
     * Creates a form to delete a ligneBonLivraison entity.
     *
     * @param LigneBonLivraison $ligneBonLivraison The ligneBonLivraison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneBonLivraison $ligneBonLivraison) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('lignebonlivraison_delete', array('id' => $ligneBonLivraison->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

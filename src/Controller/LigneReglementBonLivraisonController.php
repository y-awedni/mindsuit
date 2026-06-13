<?php

namespace App\Controller;

use App\Entity\LigneReglementBonLivraison;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignereglementbonlivraison controller.
 *
 * @Route("lignereglementbonlivraison")
 */
class LigneReglementBonLivraisonController extends BaseController
{
    /**
     * Lists all ligneReglementBonLivraison entities.
     *
     * @Route("/", name="lignereglementbonlivraison_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneReglementBonLivraisons = $em->getRepository('App\\Entity\\LigneReglementBonLivraison')->findAll();

        return $this->render('lignereglementbonlivraison/index.html.twig', array(
            'ligneReglementBonLivraisons' => $ligneReglementBonLivraisons,
        ));
    }

    /**
     * Creates a new ligneReglementBonLivraison entity.
     *
     * @Route("/new", name="lignereglementbonlivraison_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneReglementBonLivraison = new Lignereglementbonlivraison();
        $form = $this->createForm('App\Form\LigneReglementBonLivraisonType', $ligneReglementBonLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneReglementBonLivraison);
            $em->flush($ligneReglementBonLivraison);

            return $this->redirectToRoute('lignereglementbonlivraison_show', array('id' => $ligneReglementBonLivraison->getId()));
        }

        return $this->render('lignereglementbonlivraison/new.html.twig', array(
            'ligneReglementBonLivraison' => $ligneReglementBonLivraison,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneReglementBonLivraison entity.
     *
     * @Route("/{id}", name="lignereglementbonlivraison_show", methods={"GET"})
     */
    public function showAction(LigneReglementBonLivraison $ligneReglementBonLivraison)
    {
        $deleteForm = $this->createDeleteForm($ligneReglementBonLivraison);

        return $this->render('lignereglementbonlivraison/show.html.twig', array(
            'ligneReglementBonLivraison' => $ligneReglementBonLivraison,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneReglementBonLivraison entity.
     *
     * @Route("/{id}/edit", name="lignereglementbonlivraison_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneReglementBonLivraison $ligneReglementBonLivraison)
    {
        $deleteForm = $this->createDeleteForm($ligneReglementBonLivraison);
        $editForm = $this->createForm('App\Form\LigneReglementBonLivraisonType', $ligneReglementBonLivraison);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lignereglementbonlivraison_edit', array('id' => $ligneReglementBonLivraison->getId()));
        }

        return $this->render('lignereglementbonlivraison/edit.html.twig', array(
            'ligneReglementBonLivraison' => $ligneReglementBonLivraison,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneReglementBonLivraison entity.
     *
     * @Route("/{id}", name="lignereglementbonlivraison_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneReglementBonLivraison $ligneReglementBonLivraison)
    {
        $form = $this->createDeleteForm($ligneReglementBonLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneReglementBonLivraison);
            $em->flush($ligneReglementBonLivraison);
        }

        return $this->redirectToRoute('lignereglementbonlivraison_index');
    }

    /**
     * Creates a form to delete a ligneReglementBonLivraison entity.
     *
     * @param LigneReglementBonLivraison $ligneReglementBonLivraison The ligneReglementBonLivraison entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneReglementBonLivraison $ligneReglementBonLivraison)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignereglementbonlivraison_delete', array('id' => $ligneReglementBonLivraison->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

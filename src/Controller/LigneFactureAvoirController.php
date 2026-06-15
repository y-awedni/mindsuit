<?php

namespace App\Controller;

use App\Entity\LigneFactureAvoir;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignefactureavoir controller.
 *
 * @Route("lignefactureavoir")
 */
class LigneFactureAvoirController extends BaseController
{
    /**
     * Lists all ligneFactureAvoir entities.
     *
     * @Route("/", name="lignefactureavoir_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getEm();

        $ligneFactureAvoirs = $em->getRepository('App\\Entity\\LigneFactureAvoir')->findAll();

        return $this->render('lignefactureavoir/index.html.twig', array(
            'ligneFactureAvoirs' => $ligneFactureAvoirs,
        ));
    }

    /**
     * Creates a new ligneFactureAvoir entity.
     *
     * @Route("/new", name="lignefactureavoir_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneFactureAvoir = new Lignefactureavoir();
        $form = $this->createForm('App\Form\LigneFactureAvoirType', $ligneFactureAvoir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($ligneFactureAvoir);
            $em->flush($ligneFactureAvoir);

            return $this->redirectToRoute('lignefactureavoir_show', array('id' => $ligneFactureAvoir->getId()));
        }

        return $this->render('lignefactureavoir/new.html.twig', array(
            'ligneFactureAvoir' => $ligneFactureAvoir,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneFactureAvoir entity.
     *
     * @Route("/{id}", name="lignefactureavoir_show", methods={"GET"})
     */
    public function showAction(LigneFactureAvoir $ligneFactureAvoir)
    {
        $deleteForm = $this->createDeleteForm($ligneFactureAvoir);

        return $this->render('lignefactureavoir/show.html.twig', array(
            'ligneFactureAvoir' => $ligneFactureAvoir,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneFactureAvoir entity.
     *
     * @Route("/{id}/edit", name="lignefactureavoir_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneFactureAvoir $ligneFactureAvoir)
    {
        $deleteForm = $this->createDeleteForm($ligneFactureAvoir);
        $editForm = $this->createForm('App\Form\LigneFactureAvoirType', $ligneFactureAvoir);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

            return $this->redirectToRoute('lignefactureavoir_edit', array('id' => $ligneFactureAvoir->getId()));
        }

        return $this->render('lignefactureavoir/edit.html.twig', array(
            'ligneFactureAvoir' => $ligneFactureAvoir,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneFactureAvoir entity.
     *
     * @Route("/{id}", name="lignefactureavoir_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneFactureAvoir $ligneFactureAvoir)
    {
        $form = $this->createDeleteForm($ligneFactureAvoir);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->remove($ligneFactureAvoir);
            $em->flush($ligneFactureAvoir);
        }

        return $this->redirectToRoute('lignefactureavoir_index');
    }

    /**
     * Creates a form to delete a ligneFactureAvoir entity.
     *
     * @param LigneFactureAvoir $ligneFactureAvoir The ligneFactureAvoir entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneFactureAvoir $ligneFactureAvoir)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignefactureavoir_delete', array('id' => $ligneFactureAvoir->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

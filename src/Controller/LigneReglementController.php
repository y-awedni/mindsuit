<?php

namespace App\Controller;

use App\Entity\LigneReglement;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignereglement controller.
 *
 * @Route("lignereglement")
 */
class LigneReglementController extends BaseController
{
    /**
     * Lists all ligneReglement entities.
     *
     * @Route("/", name="lignereglement_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneReglements = $em->getRepository('App\\Entity\\LigneReglement')->findAll();

        return $this->render('lignereglement/index.html.twig', array(
            'ligneReglements' => $ligneReglements,
        ));
    }

    /**
     * Creates a new ligneReglement entity.
     *
     * @Route("/new", name="lignereglement_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneReglement = new Lignereglement();
        $form = $this->createForm('App\Form\LigneReglementType', $ligneReglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneReglement);
            $em->flush($ligneReglement);

            return $this->redirectToRoute('lignereglement_show', array('id' => $ligneReglement->getId()));
        }

        return $this->render('lignereglement/new.html.twig', array(
            'ligneReglement' => $ligneReglement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneReglement entity.
     *
     * @Route("/{id}", name="lignereglement_show", methods={"GET"})
     */
    public function showAction(LigneReglement $ligneReglement)
    {
        $deleteForm = $this->createDeleteForm($ligneReglement);

        return $this->render('lignereglement/show.html.twig', array(
            'ligneReglement' => $ligneReglement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneReglement entity.
     *
     * @Route("/{id}/edit", name="lignereglement_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneReglement $ligneReglement)
    {
        $deleteForm = $this->createDeleteForm($ligneReglement);
        $editForm = $this->createForm('App\Form\LigneReglementType', $ligneReglement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lignereglement_edit', array('id' => $ligneReglement->getId()));
        }

        return $this->render('lignereglement/edit.html.twig', array(
            'ligneReglement' => $ligneReglement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneReglement entity.
     *
     * @Route("/{id}", name="lignereglement_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneReglement $ligneReglement)
    {
        $form = $this->createDeleteForm($ligneReglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneReglement);
            $em->flush($ligneReglement);
        }

        return $this->redirectToRoute('lignereglement_index');
    }

    /**
     * Creates a form to delete a ligneReglement entity.
     *
     * @param LigneReglement $ligneReglement The ligneReglement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneReglement $ligneReglement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignereglement_delete', array('id' => $ligneReglement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

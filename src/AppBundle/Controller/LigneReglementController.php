<?php

namespace AppBundle\Controller;

use AppBundle\Entity\LigneReglement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignereglement controller.
 *
 * @Route("lignereglement")
 */
class LigneReglementController extends Controller
{
    /**
     * Lists all ligneReglement entities.
     *
     * @Route("/", name="lignereglement_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneReglements = $em->getRepository('AppBundle:LigneReglement')->findAll();

        return $this->render('lignereglement/index.html.twig', array(
            'ligneReglements' => $ligneReglements,
        ));
    }

    /**
     * Creates a new ligneReglement entity.
     *
     * @Route("/new", name="lignereglement_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneReglement = new Lignereglement();
        $form = $this->createForm('AppBundle\Form\LigneReglementType', $ligneReglement);
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
     * @Route("/{id}", name="lignereglement_show")
     * @Method("GET")
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
     * @Route("/{id}/edit", name="lignereglement_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, LigneReglement $ligneReglement)
    {
        $deleteForm = $this->createDeleteForm($ligneReglement);
        $editForm = $this->createForm('AppBundle\Form\LigneReglementType', $ligneReglement);
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
     * @Route("/{id}", name="lignereglement_delete")
     * @Method("DELETE")
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

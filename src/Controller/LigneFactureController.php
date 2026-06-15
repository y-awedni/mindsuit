<?php

namespace App\Controller;

use App\Entity\LigneFacture;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignefacture controller.
 *
 * @Route("lignefacture")
 */
class LigneFactureController extends BaseController
{
    /**
     * Lists all ligneFacture entities.
     *
     * @Route("/", name="lignefacture_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getEm();

        $ligneFactures = $em->getRepository('App\\Entity\\LigneFacture')->findAll();

        return $this->render('lignefacture/index.html.twig', array(
            'ligneFactures' => $ligneFactures,
        ));
    }

    /**
     * Creates a new ligneFacture entity.
     *
     * @Route("/new", name="lignefacture_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneFacture = new Lignefacture();
        $form = $this->createForm('App\Form\LigneFactureType', $ligneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($ligneFacture);
            $em->flush($ligneFacture);

            return $this->redirectToRoute('lignefacture_show', array('id' => $ligneFacture->getId()));
        }

        return $this->render('lignefacture/new.html.twig', array(
            'ligneFacture' => $ligneFacture,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneFacture entity.
     *
     * @Route("/{id}", name="lignefacture_show", methods={"GET"})
     */
    public function showAction(LigneFacture $ligneFacture)
    {
        $deleteForm = $this->createDeleteForm($ligneFacture);

        return $this->render('lignefacture/show.html.twig', array(
            'ligneFacture' => $ligneFacture,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneFacture entity.
     *
     * @Route("/{id}/edit", name="lignefacture_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneFacture $ligneFacture)
    {
        $deleteForm = $this->createDeleteForm($ligneFacture);
        $editForm = $this->createForm('App\Form\LigneFactureType', $ligneFacture);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

            return $this->redirectToRoute('lignefacture_edit', array('id' => $ligneFacture->getId()));
        }

        return $this->render('lignefacture/edit.html.twig', array(
            'ligneFacture' => $ligneFacture,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneFacture entity.
     *
     * @Route("/{id}", name="lignefacture_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneFacture $ligneFacture)
    {
        $form = $this->createDeleteForm($ligneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->remove($ligneFacture);
            $em->flush($ligneFacture);
        }

        return $this->redirectToRoute('lignefacture_index');
    }

    /**
     * Creates a form to delete a ligneFacture entity.
     *
     * @param LigneFacture $ligneFacture The ligneFacture entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneFacture $ligneFacture)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignefacture_delete', array('id' => $ligneFacture->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

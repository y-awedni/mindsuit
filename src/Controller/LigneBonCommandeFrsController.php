<?php

namespace App\Controller;

use App\Entity\LigneBonCommandeFrs;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Ligneboncommandefr controller.
 *
 * @Route("ligneboncommandefrs")
 */
class LigneBonCommandeFrsController extends BaseController
{
    /**
     * Lists all ligneBonCommandeFr entities.
     *
     * @Route("/", name="ligneboncommandefrs_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneBonCommandeFrs = $em->getRepository('App\\Entity\\LigneBonCommandeFrs')->findAll();

        return $this->render('ligneboncommandefrs/index.html.twig', array(
            'ligneBonCommandeFrs' => $ligneBonCommandeFrs,
        ));
    }

    /**
     * Creates a new ligneBonCommandeFr entity.
     *
     * @Route("/new", name="ligneboncommandefrs_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneBonCommandeFr = new Ligneboncommandefr();
        $form = $this->createForm('App\Form\LigneBonCommandeFrsType', $ligneBonCommandeFr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneBonCommandeFr);
            $em->flush($ligneBonCommandeFr);

            return $this->redirectToRoute('ligneboncommandefrs_show', array('id' => $ligneBonCommandeFr->getId()));
        }

        return $this->render('ligneboncommandefrs/new.html.twig', array(
            'ligneBonCommandeFr' => $ligneBonCommandeFr,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneBonCommandeFr entity.
     *
     * @Route("/{id}", name="ligneboncommandefrs_show", methods={"GET"})
     */
    public function showAction(LigneBonCommandeFrs $ligneBonCommandeFr)
    {
        $deleteForm = $this->createDeleteForm($ligneBonCommandeFr);

        return $this->render('ligneboncommandefrs/show.html.twig', array(
            'ligneBonCommandeFr' => $ligneBonCommandeFr,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneBonCommandeFr entity.
     *
     * @Route("/{id}/edit", name="ligneboncommandefrs_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneBonCommandeFrs $ligneBonCommandeFr)
    {
        $deleteForm = $this->createDeleteForm($ligneBonCommandeFr);
        $editForm = $this->createForm('App\Form\LigneBonCommandeFrsType', $ligneBonCommandeFr);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ligneboncommandefrs_edit', array('id' => $ligneBonCommandeFr->getId()));
        }

        return $this->render('ligneboncommandefrs/edit.html.twig', array(
            'ligneBonCommandeFr' => $ligneBonCommandeFr,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneBonCommandeFr entity.
     *
     * @Route("/{id}", name="ligneboncommandefrs_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneBonCommandeFrs $ligneBonCommandeFr)
    {
        $form = $this->createDeleteForm($ligneBonCommandeFr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneBonCommandeFr);
            $em->flush($ligneBonCommandeFr);
        }

        return $this->redirectToRoute('ligneboncommandefrs_index');
    }

    /**
     * Creates a form to delete a ligneBonCommandeFr entity.
     *
     * @param LigneBonCommandeFrs $ligneBonCommandeFr The ligneBonCommandeFr entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneBonCommandeFrs $ligneBonCommandeFr)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ligneboncommandefrs_delete', array('id' => $ligneBonCommandeFr->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

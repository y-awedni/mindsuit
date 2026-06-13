<?php

namespace App\Controller;

use App\Entity\LigneBonReception;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignebonreception controller.
 *
 * @Route("lignebonreception")
 */
class LigneBonReceptionController extends BaseController
{
    /**
     * Lists all ligneBonReception entities.
     *
     * @Route("/", name="lignebonreception_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneBonReceptions = $em->getRepository('App\\Entity\\LigneBonReception')->findAll();

        return $this->render('lignebonreception/index.html.twig', array(
            'ligneBonReceptions' => $ligneBonReceptions,
        ));
    }

    /**
     * Creates a new ligneBonReception entity.
     *
     * @Route("/new", name="lignebonreception_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneBonReception = new Lignebonreception();
        $form = $this->createForm('App\Form\LigneBonReceptionType', $ligneBonReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneBonReception);
            $em->flush($ligneBonReception);

            return $this->redirectToRoute('lignebonreception_show', array('id' => $ligneBonReception->getId()));
        }

        return $this->render('lignebonreception/new.html.twig', array(
            'ligneBonReception' => $ligneBonReception,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneBonReception entity.
     *
     * @Route("/{id}", name="lignebonreception_show", methods={"GET"})
     */
    public function showAction(LigneBonReception $ligneBonReception)
    {
        $deleteForm = $this->createDeleteForm($ligneBonReception);

        return $this->render('lignebonreception/show.html.twig', array(
            'ligneBonReception' => $ligneBonReception,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneBonReception entity.
     *
     * @Route("/{id}/edit", name="lignebonreception_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneBonReception $ligneBonReception)
    {
        $deleteForm = $this->createDeleteForm($ligneBonReception);
        $editForm = $this->createForm('App\Form\LigneBonReceptionType', $ligneBonReception);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lignebonreception_edit', array('id' => $ligneBonReception->getId()));
        }

        return $this->render('lignebonreception/edit.html.twig', array(
            'ligneBonReception' => $ligneBonReception,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneBonReception entity.
     *
     * @Route("/{id}", name="lignebonreception_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneBonReception $ligneBonReception)
    {
        $form = $this->createDeleteForm($ligneBonReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneBonReception);
            $em->flush($ligneBonReception);
        }

        return $this->redirectToRoute('lignebonreception_index');
    }

    /**
     * Creates a form to delete a ligneBonReception entity.
     *
     * @param LigneBonReception $ligneBonReception The ligneBonReception entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneBonReception $ligneBonReception)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignebonreception_delete', array('id' => $ligneBonReception->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

<?php

namespace App\Controller;

use App\Entity\LigneReglementBonReception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignereglementbonreception controller.
 *
 * @Route("lignereglementbonreception")
 */
class LigneReglementBonReceptionController extends Controller
{
    /**
     * Lists all ligneReglementBonReception entities.
     *
     * @Route("/", name="lignereglementbonreception_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneReglementBonReceptions = $em->getRepository('App\\Entity\\LigneReglementBonReception')->findAll();

        return $this->render('lignereglementbonreception/index.html.twig', array(
            'ligneReglementBonReceptions' => $ligneReglementBonReceptions,
        ));
    }

    /**
     * Creates a new ligneReglementBonReception entity.
     *
     * @Route("/new", name="lignereglementbonreception_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneReglementBonReception = new Lignereglementbonreception();
        $form = $this->createForm('App\Form\LigneReglementBonReceptionType', $ligneReglementBonReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneReglementBonReception);
            $em->flush($ligneReglementBonReception);

            return $this->redirectToRoute('lignereglementbonreception_show', array('id' => $ligneReglementBonReception->getId()));
        }

        return $this->render('lignereglementbonreception/new.html.twig', array(
            'ligneReglementBonReception' => $ligneReglementBonReception,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneReglementBonReception entity.
     *
     * @Route("/{id}", name="lignereglementbonreception_show", methods={"GET"})
     */
    public function showAction(LigneReglementBonReception $ligneReglementBonReception)
    {
        $deleteForm = $this->createDeleteForm($ligneReglementBonReception);

        return $this->render('lignereglementbonreception/show.html.twig', array(
            'ligneReglementBonReception' => $ligneReglementBonReception,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneReglementBonReception entity.
     *
     * @Route("/{id}/edit", name="lignereglementbonreception_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneReglementBonReception $ligneReglementBonReception)
    {
        $deleteForm = $this->createDeleteForm($ligneReglementBonReception);
        $editForm = $this->createForm('App\Form\LigneReglementBonReceptionType', $ligneReglementBonReception);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lignereglementbonreception_edit', array('id' => $ligneReglementBonReception->getId()));
        }

        return $this->render('lignereglementbonreception/edit.html.twig', array(
            'ligneReglementBonReception' => $ligneReglementBonReception,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneReglementBonReception entity.
     *
     * @Route("/{id}", name="lignereglementbonreception_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneReglementBonReception $ligneReglementBonReception)
    {
        $form = $this->createDeleteForm($ligneReglementBonReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneReglementBonReception);
            $em->flush($ligneReglementBonReception);
        }

        return $this->redirectToRoute('lignereglementbonreception_index');
    }

    /**
     * Creates a form to delete a ligneReglementBonReception entity.
     *
     * @param LigneReglementBonReception $ligneReglementBonReception The ligneReglementBonReception entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneReglementBonReception $ligneReglementBonReception)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignereglementbonreception_delete', array('id' => $ligneReglementBonReception->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

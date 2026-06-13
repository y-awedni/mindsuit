<?php

namespace App\Controller;

use App\Entity\Reglement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Reglement controller.
 *
 * @Route("reglement")
 */
class ReglementController extends Controller
{
    /**
     * Lists all reglement entities.
     *
     * @Route("/", name="reglement_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reglements = $em->getRepository('App\\Entity\\Reglement')->findAll();

        return $this->render('reglement/index.html.twig', array(
            'reglements' => $reglements,
        ));
    }

    /**
     * Creates a new reglement entity.
     *
     * @Route("/new", name="reglement_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $reglement = new Reglement();
        $form = $this->createForm('App\Form\ReglementType', $reglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($reglement);
            $em->flush($reglement);

            return $this->redirectToRoute('reglement_show', array('id' => $reglement->getId()));
        }

        return $this->render('reglement/new.html.twig', array(
            'reglement' => $reglement,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a reglement entity.
     *
     * @Route("/{id}", name="reglement_show", methods={"GET"})
     */
    public function showAction(Reglement $reglement)
    {
        $deleteForm = $this->createDeleteForm($reglement);

        return $this->render('reglement/show.html.twig', array(
            'reglement' => $reglement,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing reglement entity.
     *
     * @Route("/{id}/edit", name="reglement_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Reglement $reglement)
    {
        $deleteForm = $this->createDeleteForm($reglement);
        $editForm = $this->createForm('App\Form\ReglementType', $reglement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reglement_edit', array('id' => $reglement->getId()));
        }

        return $this->render('reglement/edit.html.twig', array(
            'reglement' => $reglement,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a reglement entity.
     *
     * @Route("/{id}", name="reglement_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Reglement $reglement)
    {
        $form = $this->createDeleteForm($reglement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reglement);
            $em->flush($reglement);
        }

        return $this->redirectToRoute('reglement_index');
    }

    /**
     * Creates a form to delete a reglement entity.
     *
     * @param Reglement $reglement The reglement entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reglement $reglement)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('reglement_delete', array('id' => $reglement->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

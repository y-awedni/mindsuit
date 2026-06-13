<?php

namespace App\Controller;

use App\Entity\LigneDevis;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Lignedevi controller.
 *
 * @Route("lignedevis")
 */
class LigneDevisController extends BaseController
{
    /**
     * Lists all ligneDevi entities.
     *
     * @Route("/", name="lignedevis_index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ligneDevis = $em->getRepository('App\\Entity\\LigneDevis')->findAll();

        return $this->render('lignedevis/index.html.twig', array(
            'ligneDevis' => $ligneDevis,
        ));
    }

    /**
     * Creates a new ligneDevi entity.
     *
     * @Route("/new", name="lignedevis_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $ligneDevi = new Lignedevis();
        $form = $this->createForm('App\Form\LigneDevisType', $ligneDevi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($ligneDevi);
            $em->flush($ligneDevi);

            return $this->redirectToRoute('lignedevis_show', array('id' => $ligneDevi->getId()));
        }

        return $this->render('lignedevis/new.html.twig', array(
            'ligneDevi' => $ligneDevi,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ligneDevi entity.
     *
     * @Route("/{id}", name="lignedevis_show", methods={"GET"})
     */
    public function showAction(LigneDevis $ligneDevi)
    {
        $deleteForm = $this->createDeleteForm($ligneDevi);

        return $this->render('lignedevis/show.html.twig', array(
            'ligneDevi' => $ligneDevi,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ligneDevi entity.
     *
     * @Route("/{id}/edit", name="lignedevis_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, LigneDevis $ligneDevi)
    {
        $deleteForm = $this->createDeleteForm($ligneDevi);
        $editForm = $this->createForm('App\Form\LigneDevisType', $ligneDevi);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('lignedevis_edit', array('id' => $ligneDevi->getId()));
        }

        return $this->render('lignedevis/edit.html.twig', array(
            'ligneDevi' => $ligneDevi,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ligneDevi entity.
     *
     * @Route("/{id}", name="lignedevis_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, LigneDevis $ligneDevi)
    {
        $form = $this->createDeleteForm($ligneDevi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($ligneDevi);
            $em->flush($ligneDevi);
        }

        return $this->redirectToRoute('lignedevis_index');
    }

    /**
     * Creates a form to delete a ligneDevi entity.
     *
     * @param LigneDevis $ligneDevi The ligneDevi entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(LigneDevis $ligneDevi)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('lignedevis_delete', array('id' => $ligneDevi->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

<?php

namespace App\Controller;

use App\Entity\Societe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Societe controller.
 *
 * @Route("societe")
 */
class SocieteController extends Controller {

    /**
     * Lists all societe entities.
     *
     * @Route("/", name="societe_index", methods={"GET","POST"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $societe = $em->getRepository('App\\Entity\\Societe')->find(1);

        $deleteForm = $this->createDeleteForm($societe);
        $editForm = $this->createForm('App\Form\SocieteType', $societe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($societe ->getMedia()) {
                $societe ->getMedia()->setName(sha1(uniqid(mt_rand(), true)));
            }
        	
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('societe_index');
        }

        return $this->render('societe/edit.html.twig', array(
                    'societe' => $societe,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing societe entity.
     *
     * @Route("/{id}/edit", name="societe_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Societe $societe) {
        $deleteForm = $this->createDeleteForm($societe);
        $editForm = $this->createForm('App\Form\SocieteType', $societe);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if($societe->getMedia()){
                $societe->getMedia()->setName(sha1(uniqid(mt_rand(), true)));
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('societe_edit', array('id' => $societe->getId()));
        }

        return $this->render('societe/edit.html.twig', array(
                    'societe' => $societe,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a societe entity.
     *
     * @Route("/{id}", name="societe_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Societe $societe) {
        $form = $this->createDeleteForm($societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($societe);
            $em->flush($societe);
        }

        return $this->redirectToRoute('societe_index');
    }

    /**
     * Creates a form to delete a societe entity.
     *
     * @param Societe $societe The societe entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Societe $societe) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('societe_delete', array('id' => $societe->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}

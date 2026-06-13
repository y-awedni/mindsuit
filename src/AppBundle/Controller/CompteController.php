<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Compte controller.
 *
 * @Route("compte")
 */
class CompteController extends Controller {

    /**
     * Lists all compte entities.
     *
     * @Route("/", name="compte_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $comptes = $em->getRepository('AppBundle:Compte')->findBy([], ['id' => 'DESC']);

        return $this->render('compte/index.html.twig', array(
                    'comptes' => $comptes,
        ));
    }

    /**
     * Creates a new compte entity.
     *
     * @Route("/new", name="compte_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $compte = new Compte();
        $form = $this->createForm('AppBundle\Form\CompteType', $compte);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($compte);
            $em->flush($compte);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('compte_new');
            }
            return $this->redirectToRoute('compte_index');
        }

        return $this->render('compte/new.html.twig', array(
                    'compte' => $compte,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing compte entity.
     *
     * @Route("/{id}/edit", name="compte_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Compte $compte) {
        $editForm = $this->createForm('AppBundle\Form\CompteType', $compte);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compte_index');
        }
        return $this->render('compte/edit.html.twig', array(
                    'compte' => $compte,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Deletes a compte entity.
     *
     * @Route("/{id}/delete", name="compte_delete")
     * @Method("GET")
     */
    public function deleteAction(Compte $compte) {
        if ($compte) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($compte);
                $em->flush($compte);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('compte_index');
    }
}

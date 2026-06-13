<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Unite;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unite controller.
 *
 * @Route("unite")
 */
class UniteController extends Controller {

    /**
     * Lists all unite entities.
     *
     * @Route("/", name="unite_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $unites = $em->getRepository('AppBundle:Unite')->findBy([], ['id' => 'DESC']);
        return $this->render('unite/index.html.twig', array(
                    'unites' => $unites,
        ));
    }

    /**
     * Creates a new unite entity.
     *
     * @Route("/new", name="unite_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $unite = new Unite();
        $form = $this->createForm('AppBundle\Form\UniteType', $unite);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($unite);
            $em->flush($unite);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('unite_new');
            }
            return $this->redirectToRoute('unite_index');
        }

        return $this->render('unite/new.html.twig', array(
                    'unite' => $unite,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing unite entity.
     *
     * @Route("/{id}/edit", name="unite_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Unite $unite) {
        $editForm = $this->createForm('AppBundle\Form\UniteType', $unite);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('unite_index');
        }

        return $this->render('unite/edit.html.twig', array(
                    'unite' => $unite,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a unite entity.
     *
     * @Route("/{id}/delete", name="unite_delete", methods={"GET"})
     */
    public function deleteAction(Unite $unite) {
        if ($unite) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($unite);
                $em->flush($unite);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('unite_index');
    }

}

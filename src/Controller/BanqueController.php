<?php

namespace App\Controller;

use App\Entity\Banque;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Banque controller.
 *
 * @Route("banque")
 */
class BanqueController extends BaseController {

    /**
     * Lists all banque entities.
     *
     * @Route("/", name="banque_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $banques = $em->getRepository('App\\Entity\\Banque')->findBy([], ['id' => 'DESC']);

        return $this->render('banque/index.html.twig', array(
                    'banques' => $banques,
        ));
    }

    /**
     * Creates a new banque entity.
     *
     * @Route("/new", name="banque_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $banque = new Banque();
        $form = $this->createForm('App\Form\BanqueType', $banque);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($banque);
            $em->flush($banque);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('banque_new');
            }
            return $this->redirectToRoute('banque_index');
        }

        return $this->render('banque/new.html.twig', array(
                    'banque' => $banque,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing banque entity.
     *
     * @Route("/{id}/edit", name="banque_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Banque $banque) {
        $editForm = $this->createForm('App\Form\BanqueType', $banque);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('banque_index');
        }

        return $this->render('banque/edit.html.twig', array(
                    'banque' => $banque,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a banque entity.
     *
     * @Route("/{id}/delete", name="banque_delete", methods={"GET"})
     */
    public function deleteAction(Banque $banque) {
        if ($banque) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($banque);
                $em->flush($banque);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('banque_index');
    }

}

<?php

namespace App\Controller;

use App\Entity\Tva;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tva controller.
 *
 * @Route("tva")
 */
class TvaController extends BaseController {

    /**
     * Lists all tva entities.
     *
     * @Route("/", name="tva_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getEm();

        $tvas = $em->getRepository('App\\Entity\\Tva')->findBy([], ['id' => 'DESC']);

        return $this->render('tva/index.html.twig', array(
                    'tvas' => $tvas,
        ));
    }

    /**
     * Creates a new tva entity.
     *
     * @Route("/new", name="tva_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $tva = new Tva();
        $form = $this->createForm('App\Form\TvaType', $tva);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($tva);
            $em->flush($tva);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('tva_new');
            }
            return $this->redirectToRoute('tva_index');
        }

        return $this->render('tva/new.html.twig', array(
                    'tva' => $tva,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing tva entity.
     *
     * @Route("/{id}/edit", name="tva_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Tva $tva) {
        $editForm = $this->createForm('App\Form\TvaType', $tva);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

            return $this->redirectToRoute('tva_index');
        }

        return $this->render('tva/edit.html.twig', array(
                    'tva' => $tva,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a tva entity.
     *
     * @Route("/{id}/delete", name="tva_delete", methods={"GET"})
     */
    public function deleteAction(Tva $tva) {
        if ($tva) {
            try {
                $em = $this->getEm();
                $em->remove($tva);
                $em->flush($tva);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('tva_index');
    }

}

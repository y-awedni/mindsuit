<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Magasin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Magasin controller.
 *
 * @Route("magasin")
 */
class MagasinController extends Controller {

    /**
     * Lists all magasin entities.
     *
     * @Route("/", name="magasin_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $magasins = $em->getRepository('AppBundle:Magasin')->findAll();

        return $this->render('magasin/index.html.twig', array(
                    'magasins' => $magasins,
        ));
    }

    /**
     * Creates a new magasin entity.
     *
     * @Route("/new", name="magasin_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $magasin = new Magasin();
        $form = $this->createForm('AppBundle\Form\MagasinType', $magasin);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($magasin);
            $em->flush($magasin);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('magasin_new');
            }
            return $this->redirectToRoute('magasin_index');
        }

        return $this->render('magasin/new.html.twig', array(
                    'magasin' => $magasin,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing magasin entity.
     *
     * @Route("/{id}/edit", name="magasin_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Magasin $magasin) {
        $editForm = $this->createForm('AppBundle\Form\MagasinType', $magasin);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('magasin_index');
        }

        return $this->render('magasin/edit.html.twig', array(
                    'magasin' => $magasin,
                    'edit_form' => $editForm->createView()
        ));
    }

    
    /**
     * Deletes a magasin entity.
     *
     * @Route("/{id}/delete", name="magasin_delete")
     * @Method("GET")
     */
    public function deleteAction(Magasin $magasin) {
        if ($magasin) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($magasin);
                $em->flush($magasin);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('magasin_index');
    }
}

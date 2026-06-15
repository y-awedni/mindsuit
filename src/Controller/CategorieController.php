<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorie controller.
 *
 * @Route("categorie")
 */
class CategorieController extends BaseController {

    /**
     * Lists all categorie entities.
     *
     * @Route("/", name="categorie_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getEm();

        $categories = $em->getRepository('App\\Entity\\Categorie')->findBy([], ['id' => 'DESC']);

        return $this->render('categorie/index.html.twig', array(
                    'categories' => $categories,
        ));
    }

    /**
     * Creates a new categorie entity.
     *
     * @Route("/new", name="categorie_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $categorie = new Categorie();
        $form = $this->createForm('App\Form\CategorieType', $categorie);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($categorie);
            $em->flush($categorie);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('categorie_new');
            }
            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/new.html.twig', array(
                    'categorie' => $categorie,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing categorie entity.
     *
     * @Route("/{id}/edit", name="categorie_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Categorie $categorie) {
        $editForm = $this->createForm('App\Form\CategorieType', $categorie);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

            return $this->redirectToRoute('categorie_index');
        }

        return $this->render('categorie/edit.html.twig', array(
                    'categorie' => $categorie,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a categorie entity.
     *
     * @Route("/{id}/delete", name="categorie_delete", methods={"GET"})
     */
    public function deleteAction(Categorie $categorie) {
        if ($categorie) {
            try {
                $em = $this->getEm();
                $em->remove($categorie);
                $em->flush($categorie);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('categorie_index');
    }

}

<?php

namespace App\Controller;

use App\Entity\Famille;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Famille controller.
 *
 * @Route("famille")
 */
class FamilleController extends BaseController {

    /**
     * Lists all famille entities.
     *
     * @Route("/", name="famille_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getEm();

        $familles = $em->getRepository('App\\Entity\\Famille')->findBy([], ['id' => 'DESC']);

        return $this->render('famille/index.html.twig', array(
                    'familles' => $familles,
        ));
    }

    /**
     * Creates a new famille entity.
     *
     * @Route("/new", name="famille_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $famille = new Famille();
        $form = $this->createForm('App\Form\FamilleType', $famille);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
            $em->persist($famille);
            $em->flush($famille);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('famille_new');
            }
            return $this->redirectToRoute('famille_index');
        }

        return $this->render('famille/new.html.twig', array(
                    'famille' => $famille,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing famille entity.
     *
     * @Route("/{id}/edit", name="famille_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Famille $famille) {
        $editForm = $this->createForm('App\Form\FamilleType', $famille);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

            return $this->redirectToRoute('famille_index');
        }

        return $this->render('famille/edit.html.twig', array(
                    'famille' => $famille,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a famille entity.
     *
     * @Route("/{id}/delete", name="famille_delete", methods={"GET"})
     */
    public function deleteAction(Famille $famille) {
        if ($famille) {
            try {
                $em = $this->getEm();
                $em->remove($famille);
                $em->flush($famille);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('famille_index');
    }

    /**
     * Get famille by categorie
     *
     * @Route("/api/categorie", name="famille_categorie_api", methods={"GET"})
     */
    public function getFamilleByCategorieAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getEm();
        $categorie = $request->query->get('categorie');
        $familles = $em->getRepository('App\\Entity\\Famille')->findByCategorie($categorie);

        $serializer = $this->container->get('serializer');

        $response = array(
            'success' => true,
            'familles' => $serializer->serialize($familles, 'json', array('groups' => array('famille')))
        );
        return new JsonResponse($response);
    }
}

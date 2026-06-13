<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Famille;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Famille controller.
 *
 * @Route("famille")
 */
class FamilleController extends Controller {

    /**
     * Lists all famille entities.
     *
     * @Route("/", name="famille_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $familles = $em->getRepository('AppBundle:Famille')->findBy([], ['id' => 'DESC']);

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
        $form = $this->createForm('AppBundle\Form\FamilleType', $famille);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
        $editForm = $this->createForm('AppBundle\Form\FamilleType', $famille);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

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
                $em = $this->getDoctrine()->getManager();
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
        $em = $this->getDoctrine()->getManager();
        $categorie = $request->query->get('categorie');
        $familles = $em->getRepository('AppBundle:Famille')->findByCategorie($categorie);

        $serializer = $this->container->get('serializer');

        $response = array(
            'success' => true,
            'familles' => $serializer->serialize($familles, 'json', array('groups' => array('famille')))
        );
        return new JsonResponse($response);
    }
}

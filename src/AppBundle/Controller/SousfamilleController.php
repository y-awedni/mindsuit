<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Sousfamille;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Sousfamille controller.
 *
 * @Route("sousfamille")
 */
class SousfamilleController extends Controller {

    /**
     * Lists all sousfamille entities.
     *
     * @Route("/", name="sousfamille_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $sousfamilles = $em->getRepository('AppBundle:Sousfamille')->findBy([], ['id' => 'DESC']);

        return $this->render('sousfamille/index.html.twig', array(
                    'sousfamilles' => $sousfamilles,
        ));
    }

    /**
     * Creates a new sousfamille entity.
     *
     * @Route("/new", name="sousfamille_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request) {
        $sousfamille = new Sousfamille();
        $form = $this->createForm('AppBundle\Form\SousfamilleType', $sousfamille);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sousfamille);
            $em->flush($sousfamille);
            if ($form->get('saveAndNew')->isClicked()) {
                return $this->redirectToRoute('sousfamille_new');
            }
            return $this->redirectToRoute('sousfamille_index');
        }

        return $this->render('sousfamille/new.html.twig', array(
                    'sousfamille' => $sousfamille,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sousfamille entity.
     *
     * @Route("/{id}/edit", name="sousfamille_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Sousfamille $sousfamille) {
        
        $editForm = $this->createForm('AppBundle\Form\SousfamilleType', $sousfamille);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sousfamille_index');
        }

        return $this->render('sousfamille/edit.html.twig', array(
                    'sousfamille' => $sousfamille,
                    'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a sousfamille entity.
     *
     * @Route("/{id}/delete", name="sousfamille_delete", methods={"GET"})
     */
    public function deleteAction(Sousfamille $sousfamille) {
        if ($sousfamille) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->remove($sousfamille);
                $em->flush($sousfamille);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->get('session')->getFlashBag()->add('error', $this->get('translator')->trans("Can't delete entity"));
            }
        }
        return $this->redirectToRoute('sousfamille_index');
    }
    
    
    
    /**
     * Get sous famille by famille
     *
     * @Route("/api/famille", name="sousfamille_famille_api", methods={"GET"})
     */
    public function getSousFamilleByFamilleAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getDoctrine()->getManager();
        $famille = $request->query->get('famille');
        $sousfamilles = $em->getRepository('AppBundle:Sousfamille')->findByFamille($famille);

        $serializer = $this->container->get('serializer');

        $response = array(
            'success' => true,
            'sousfamilles' => $serializer->serialize($sousfamilles, 'json', array('groups' => array('sousfamille')))
        );
        return new JsonResponse($response);
    }

}

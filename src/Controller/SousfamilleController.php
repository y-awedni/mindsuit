<?php

namespace App\Controller;

use App\Entity\Sousfamille;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Sousfamille controller.
 *
 * @Route("sousfamille")
 */
class SousfamilleController extends BaseController {

    /**
     * Lists all sousfamille entities.
     *
     * @Route("/", name="sousfamille_index", methods={"GET"})
     */
    public function indexAction() {
        $em = $this->getEm();

        $sousfamilles = $em->getRepository('App\\Entity\\Sousfamille')->findBy([], ['id' => 'DESC']);

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
        $form = $this->createForm('App\Form\SousfamilleType', $sousfamille);
        $form->add('saveAndNew', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, array(
            'label' => 'Save and add a new', 'attr' => array('class' => 'btn-success fa fa-save btn-lg')
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getEm();
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
        
        $editForm = $this->createForm('App\Form\SousfamilleType', $sousfamille);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getEm()->flush();

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
                $em = $this->getEm();
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
        $em = $this->getEm();
        $famille = $request->query->get('famille');
        $sousfamilles = $em->getRepository('App\\Entity\\Sousfamille')->findByFamille($famille);

        $serializer = $this->container->get('serializer');

        $response = array(
            'success' => true,
            'sousfamilles' => $serializer->serialize($sousfamilles, 'json', array('groups' => array('sousfamille')))
        );
        return new JsonResponse($response);
    }

}

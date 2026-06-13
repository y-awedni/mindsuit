<?php

namespace App\Controller;

use App\Entity\Timbre;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Timbre fiscal (stamp duty) configuration — a single editable value.
 *
 * @Route("timbre")
 */
class TimbreController extends BaseController
{
    /**
     * Show and edit the single timbre value.
     *
     * @Route("/", name="timbre_index", methods={"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $timbre = $em->getRepository('App\\Entity\\Timbre')->findOneBy([]);
        if (null === $timbre) {
            $timbre = new Timbre();
            $em->persist($timbre);
        }

        $form = $this->createForm('App\Form\TimbreType', $timbre);
        $form->add('save', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
            'label' => 'Enregistrer', 'attr' => ['class' => 'btn-success fa fa-save btn-lg'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timbre->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('notice', 'Timbre fiscal mis à jour.');

            return $this->redirectToRoute('timbre_index');
        }

        return $this->render('timbre/index.html.twig', [
            'timbre' => $timbre,
            'form' => $form->createView(),
        ]);
    }
}

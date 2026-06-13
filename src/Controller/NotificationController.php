<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Notification controller.
 *
 * @Route("notification")
 */
class NotificationController extends BaseController {

    /**
     * Lists all notification entities.
     *
     * @Route("/", name="notification_index", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('App\\Entity\\Notification')->createQueryBuilder('a');
        //filter
        if ($request->query->get('description')) {
            $qb->where('a.description like :description')->setParameter('description', '%' . $request->query->get('description') . '%');
        }
        if ($request->query->get('vu')) {
            $vu = $request->query->get('vu');
            switch ($vu) {
                case '1':
                    $qb->andWhere('a.vu = true');
                    break;
                case '2':
                    $qb->andWhere('a.vu = false');
                    break;
            }
        }

        if (!$request->get('sort')) {
            $qb->orderBy('a.id', 'DESC');
        }
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, $request->query->getInt('page', 1), $request->query->getInt('limit', 10)
        );

        return $this->render('notification/index.html.twig', array(
                    'pagination' => $pagination,
        ));
    }

    /**
     * get notifications news
     * @Route("/news",name="notification_news", methods={"GET"})
     */
    public function newsAction() {
        $em = $this->getDoctrine()->getManager();
        $notifications = $notifications = $em->getRepository('App\\Entity\\Notification')->findBy([], ['id' => 'DESC'], '8');
        $countAlerts = count($em->getRepository('App\\Entity\\Notification')->findByVu(false));
        return $this->render('notification/news.html.twig', array(
                    'notifications' => $notifications,
                    'countAlerts' => $countAlerts
        ));
    }

    /**
     * Finds and displays a notification entity.
     *
     * @Route("/vu/all", name="notification_vu_all", methods={"GET"})
     */
    public function vuAllAction() {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository('App\\Entity\\Notification')->findByVu(false);
        foreach ($notifications as $notification) {
            $notification->setVu(true);
            $em->flush($notification);
        }
        return $this->redirectToRoute('notification_index');
    }

    /**
     * 
     *
     * @Route("change/vu/true", name="changeNotificationVu", methods={"POST"})
     */
    public function changeNotificationVuAction(Request $request) {
        if (!$request->isXmlHttpRequest()) {
            $myresponse = array(
                'success' => false,
                'content' => 'Ajax obligatoire'
            );
            return new JsonResponse($myresponse);
        }
        $em = $this->getDoctrine()->getManager();
        $notification = $em->getRepository('App\\Entity\\Notification')->find($request->request->get('id'));
        $notification->setVu(true);
        $em->flush($notification);
        $response = array(
            'success' => true
        );
        return new JsonResponse($response);
    }

}

<?php

namespace AppBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 *
 * @Route("stats/client")
 */
class ClientController extends Controller {

    public function dixClientsPlusBenefiques($em, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('c1.code as code1', 'c2.code as code2', 'SUM(s.ttc) as ttc')
                ->from('AppBundle:Stock', 's')
                ->leftjoin('s.facture', 'f')
                ->leftjoin('f.client', 'c1')
                ->leftjoin('s.bonLivraison', 'bl')
                ->leftjoin('bl.client', 'c2')
                ->where('s.mouvement = false')
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('facture', 'bl'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('s.client')
                ->orderBy('ttc', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        return $result;
    }

    public function dixClientsPlusVendus($em, $startDateCreation, $endDateCreation) {
        $result = $dixClientsPlusVendus = $em->createQueryBuilder()
                ->select('c1.code as code1', 'c2.code as code2', 'SUM(s.qte) as qte')
                ->from('AppBundle:Stock', 's')
                ->leftjoin('s.facture', 'f')
                ->leftjoin('f.client', 'c1')
                ->leftjoin('s.bonLivraison', 'bl')
                ->leftjoin('bl.client', 'c2')
                ->where('s.mouvement = false')
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('facture', 'bl'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('s.client')
                ->orderBy('qte', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        return $result;
    }

    /**
     * Lists all client entities.
     *
     * @Route("/", name="clients_stats", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dateFormat = $this->get('app.format_date');
        $startDateCreation = null;
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
        }
        $endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
        }

        $dixClientsPlusBenefiques = $this->dixClientsPlusBenefiques($em, $startDateCreation, $endDateCreation);
        $dixClientsPlusVendus = $this->dixClientsPlusVendus($em, $startDateCreation, $endDateCreation);
        $clients = $em->getRepository('AppBundle:Client')->findAll();
        return $this->render('stats/client/index.html.twig', array(
                    'dixClientsPlusVendus' => $dixClientsPlusVendus,
                    'dixClientsPlusBenefiques' => $dixClientsPlusBenefiques,
                    'clients' => $clients
        ));
    }

    public function dixArticlesPlusBenefiques($em, $client, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('a.code as code', 'a.designation as designation', 'SUM(s.ttc) as ttc')
                ->from('AppBundle:Stock', 's')
                ->join('s.article', 'a')
                ->where('s.mouvement = false')
                ->andWhere('s.client=:client')
                ->setParameter('client', $client)
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('facture', 'bl'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('s.article')
                ->orderBy('ttc', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        return $result;
    }

    public function dixArticlesPlusVendus($em, $client, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('a.code as code', 'a.designation as designation', 'SUM(s.qte) as qte')
                ->from('AppBundle:Stock', 's')
                ->join('s.article', 'a')
                ->where('s.mouvement = false')
                ->andWhere('s.client=:client')
                ->setParameter('client', $client)
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('facture', 'bl'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('s.article')
                ->orderBy('qte', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        return $result;
    }

    public function categoriesQteVendus($em, $client, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('sum(s.qte) as qte', 'c.libelle as categorie')
                ->from('AppBundle:Stock', 's')
                ->leftjoin('s.article', 'a')
                ->leftjoin('a.categorie', 'c')
                ->where('s.typeDoc IN (:typeDoc)')
                ->andWhere('s.client=:client')
                ->setParameter('client', $client)
                ->setParameter('typeDoc', array('bl', 'facture'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('a.categorie')
                ->orderBy('categorie', 'DESC')
                ->getQuery()
                ->getResult();
        return $result;
    }

    public function categoriesTtcVendus($em, $client, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('sum(s.ttc) as ttc', 'c.libelle as categorie')
                ->from('AppBundle:Stock', 's')
                ->leftjoin('s.article', 'a')
                ->leftjoin('a.categorie', 'c')
                ->where('s.typeDoc IN (:typeDoc)')
                ->andWhere('s.client=:client')
                ->setParameter('client', $client)
                ->setParameter('typeDoc', array('bl', 'facture'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('a.categorie')
                ->orderBy('categorie', 'DESC')
                ->getQuery()
                ->getResult();
        return $result;
    }

    /**
     * Show client stats.
     *
     * @Route("/{id}", name="client_stats_show", methods={"GET"})
     */
    public function showAction(Request $request) {
        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $client = $em->getRepository('AppBundle:Client')->find($id);
        if (!$client) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut sélectionner un client.');
            return $this->redirectToRoute('clients_stats');
        }
        $dateFormat = $this->get('app.format_date');
        $startDateCreation = null;
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
        }
        $endDateCreation = null;
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
        }
        $dixArticlesPlusBenefiques = $this->dixArticlesPlusBenefiques($em, $client, $startDateCreation, $endDateCreation);
        $dixArticlesPlusVendus = $this->dixArticlesPlusVendus($em, $client, $startDateCreation, $endDateCreation);
        $categoriesQteVendus = $this->categoriesQteVendus($em, $client, $startDateCreation, $endDateCreation);
        $categoriesTtcVendus = $this->categoriesTtcVendus($em, $client, $startDateCreation, $endDateCreation);
        return $this->render('stats/client/show.html.twig', array(
                    'dixArticlesPlusVendus' => $dixArticlesPlusVendus,
                    'dixArticlesPlusBenefiques' => $dixArticlesPlusBenefiques,
                    'categoriesQteVendus' => $categoriesQteVendus,
                    'categoriesTtcVendus' => $categoriesTtcVendus,
                    'idClt' => $id
        ));
    }

    /**
     * redirect
     *
     * @Route("/id", name="client_stats_id", methods={"GET"})
     */
    public function statsAction(Request $request) {
        return $this->redirectToRoute('client_stats_show', array('id' => $request->query->get('id')));
    }

}

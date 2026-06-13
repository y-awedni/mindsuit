<?php

namespace App\Controller\Stats;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Fournisseur controller.
 *
 * @Route("stats/fournisseur")
 */
class FournisseurController extends BaseController {

    /**
     * Lists all fournisseur entities.
     *
     * @Route("/", name="fournisseurs_stats", methods={"GET"})
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dateFormat = $this->get('app.format_date');
        $dixFournisseursPlusBenefiques = $em->createQueryBuilder()
                ->select('frs.code as code', 'SUM(s.ttc) as ttc')
                ->from('App\\Entity\\Stock', 's')
                ->leftjoin('s.bonReception', 'br')
                ->leftjoin('br.fournisseur', 'frs')
                ->where('s.mouvement = true')
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('br'));
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            $dixFournisseursPlusBenefiques = $dixFournisseursPlusBenefiques
                    ->andWhere('s.dateCreation >= :startDateCreation')
                    ->setParameter('startDateCreation', $startDateCreation);
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            $dixFournisseursPlusBenefiques = $dixFournisseursPlusBenefiques
                    ->andWhere('s.dateCreation <= :endDateCreation')
                    ->setParameter('endDateCreation', $endDateCreation);
        }
        $dixFournisseursPlusBenefiques = $dixFournisseursPlusBenefiques->groupBy('br.fournisseur')
                ->orderBy('ttc', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();

        $dixFournisseursPlusVendus = $em->createQueryBuilder()
                ->select('frs.code as code', 'SUM(s.qte) as qte')
                ->from('App\\Entity\\Stock', 's')
                ->leftjoin('s.bonReception', 'br')
                ->leftjoin('br.fournisseur', 'frs')
                ->where('s.mouvement = true')
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('br'));
        if ($request->query->get('startDateCreation')) {
            $startDateCreation = $dateFormat->formatDate($request->query->get('startDateCreation'));
            $dixFournisseursPlusVendus = $dixFournisseursPlusVendus
                    ->andWhere('s.dateCreation >= :startDateCreation')
                    ->setParameter('startDateCreation', $startDateCreation);
        }
        if ($request->query->get('endDateCreation')) {
            $endDateCreation = $dateFormat->formatDate($request->query->get('endDateCreation'));
            $dixFournisseursPlusVendus = $dixFournisseursPlusVendus
                    ->andWhere('s.dateCreation <= :endDateCreation')
                    ->setParameter('endDateCreation', $endDateCreation);
        }
        $dixFournisseursPlusVendus = $dixFournisseursPlusVendus->groupBy('br.fournisseur')
                ->orderBy('qte', 'DESC')
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        $fournisseurs = $em->getRepository('App\\Entity\\Fournisseur')->findAll();
        return $this->render('stats/fournisseur/index.html.twig', array(
                    'dixFournisseursPlusBenefiques' => $dixFournisseursPlusBenefiques,
                    'dixFournisseursPlusVendus' => $dixFournisseursPlusVendus,
                    'fournisseurs' => $fournisseurs
        ));
    }
    
    public function dixArticlesPlusBenefiques($em, $fournisseur, $startDateCreation, $endDateCreation){
        $result = $em->createQueryBuilder()
                ->select('a.code as code', 'a.designation as designation', 'SUM(s.ttc) as ttc')
                ->from('App\\Entity\\Stock', 's')
                ->join('s.article', 'a')
                ->where('s.mouvement = true')
                ->andWhere('s.fournisseur=:fournisseur')
                ->setParameter('fournisseur', $fournisseur)
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('br'));
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

    public function dixArticlesPlusVendus($em, $fournisseur, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('a.code as code', 'a.designation as designation', 'SUM(s.qte) as qte')
                ->from('App\\Entity\\Stock', 's')
                ->join('s.article', 'a')
                ->where('s.mouvement = true')
                ->andWhere('s.fournisseur=:fournisseur')
                ->setParameter('fournisseur', $fournisseur)
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('br'));
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

    public function categoriesQteVendus($em, $fournisseur, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('sum(s.qte) as qte', 'c.libelle as categorie')
                ->from('App\\Entity\\Stock', 's')
                ->leftjoin('s.article', 'a')
                ->leftjoin('a.categorie', 'c')
                ->where('s.typeDoc IN (:typeDoc)')
                ->andWhere('s.fournisseur=:fournisseur')
                ->setParameter('fournisseur', $fournisseur)
                ->setParameter('typeDoc', array('br'));
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

    public function categoriesTtcVendus($em, $fournisseur, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('sum(s.ttc) as ttc', 'c.libelle as categorie')
                ->from('App\\Entity\\Stock', 's')
                ->leftjoin('s.article', 'a')
                ->leftjoin('a.categorie', 'c')
                ->where('s.typeDoc IN (:typeDoc)')
                ->andWhere('s.fournisseur=:fournisseur')
                ->setParameter('fournisseur', $fournisseur)
                ->setParameter('typeDoc', array('br'));
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
     * @Route("/{id}", name="fournisseur_stats_show", methods={"GET"})
     */
    public function showAction(Request $request) {
        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $fournisseur = $em->getRepository('App\\Entity\\Fournisseur')->find($id);
        if (!$fournisseur) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut sélectionner un client.');
            return $this->redirectToRoute('fournisseurs_stats');
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
        $dixArticlesPlusBenefiques = $this->dixArticlesPlusBenefiques($em, $fournisseur, $startDateCreation, $endDateCreation);
        $dixArticlesPlusVendus = $this->dixArticlesPlusVendus($em, $fournisseur, $startDateCreation, $endDateCreation);
        $categoriesQteVendus = $this->categoriesQteVendus($em, $fournisseur, $startDateCreation, $endDateCreation);
        $categoriesTtcVendus = $this->categoriesTtcVendus($em, $fournisseur, $startDateCreation, $endDateCreation);
        return $this->render('stats/fournisseur/show.html.twig', array(
                    'dixArticlesPlusVendus' => $dixArticlesPlusVendus,
                    'dixArticlesPlusBenefiques' => $dixArticlesPlusBenefiques,
                    'categoriesQteVendus' => $categoriesQteVendus,
                    'categoriesTtcVendus' => $categoriesTtcVendus,
                    'idFrs' => $id
        ));
    }

    /**
     * redirect
     *
     * @Route("/id", name="fournisseur_stats_id", methods={"GET"})
     */
    public function statsAction(Request $request) {
        return $this->redirectToRoute('fournisseur_stats_show', array('id' => $request->query->get('id')));
    }

}

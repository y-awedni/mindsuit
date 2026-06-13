<?php

namespace AppBundle\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Article controller.
 *
 * @Route("stats/article")
 */
class ArticleController extends Controller {

    public function dixArticlesPlusBenefiques($em, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('a.code as code', 'a.designation as designation', 'SUM(s.ttc) as ttc')
                ->from('AppBundle:Stock', 's')
                ->join('s.article', 'a')
                ->where('s.mouvement = false')
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

    public function dixArticlesPlusVendus($em, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('a.code as code', 'a.designation as designation', 'SUM(s.qte) as qte')
                ->from('AppBundle:Stock', 's')
                ->join('s.article', 'a')
                ->where('s.mouvement = false')
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

    public function nombreArticlesEnEtatNormal($em) {
        return $em->createQueryBuilder()
                        ->select('COUNT(a.id) as nmbre')
                        ->from('AppBundle:Article', 'a')
                        ->where('a.qteEnStock >a.seuilAlert')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function nombreArticlesEnAlerteStock($em) {
        return $em->createQueryBuilder()
                        ->select('COUNT(a.id) as nmbre')
                        ->from('AppBundle:Article', 'a')
                        ->where('a.stockable =true')
                        ->andWhere('a.qteEnStock <= a.seuilAlert')
                        ->andWhere('a.qteEnStock > 0')
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function nombreArticlesEnRuptureStock($em) {
        return $em->createQueryBuilder()
                        ->select('COUNT(a.id) as nmbre')
                        ->from('AppBundle:Article', 'a')
                        ->where('a.stockable =true')
                        ->andWhere('a.qteEnStock IN (:qteEnStock)')
                        ->setParameter('qteEnStock', array(0, null))
                        ->getQuery()
                        ->getSingleScalarResult();
    }

    public function categoriesAchat($em) {
        return $em->createQueryBuilder()
                        ->select('sum(s.ttc) as ttc', 'c.libelle as categorie')
                        ->from('AppBundle:Stock', 's')
                        ->leftjoin('s.article', 'a')
                        ->leftjoin('a.categorie', 'c')
                        ->andWhere('s.typeDoc IN (:typeDoc)')
                        ->setParameter('typeDoc', array('br', 'ca'))
                        ->groupBy('a.categorie')
                        ->orderBy('categorie', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

    public function categoriesVente($em) {
        return $em->createQueryBuilder()
                        ->select('sum(s.ttc) as ttc', 'c.libelle as categorie')
                        ->from('AppBundle:Stock', 's')
                        ->leftjoin('s.article', 'a')
                        ->leftjoin('a.categorie', 'c')
                        ->andWhere('s.typeDoc IN (:typeDoc)')
                        ->setParameter('typeDoc', array('bl', 'facture'))
                        ->groupBy('a.categorie')
                        ->orderBy('categorie', 'DESC')
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Lists all article entities.
     *
     * @Route("/", name="articles_stats")
     * @Method("GET")
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
        $dixArticlesPlusBenefiques = $this->dixArticlesPlusBenefiques($em, $startDateCreation, $endDateCreation);
        $dixArticlesPlusVendus = $this->dixArticlesPlusVendus($em, $startDateCreation, $endDateCreation);
        $nombreArticlesEnEtatNormal = $this->nombreArticlesEnEtatNormal($em);
        $nombreArticlesEnAlerteStock = $this->nombreArticlesEnAlerteStock($em);
        $nombreArticlesEnRuptureStock = $this->nombreArticlesEnRuptureStock($em);
        $categoriesAchat = $this->categoriesAchat($em);
        $categoriesVente = $this->categoriesVente($em);
        $articles = $em->getRepository('AppBundle:Article')->findAll();
        return $this->render('stats/article/index.html.twig', array(
                    'dixArticlesPlusVendus' => $dixArticlesPlusVendus,
                    'dixArticlesPlusBenefiques' => $dixArticlesPlusBenefiques,
                    'nombreArticlesEnEtatNormal' => $nombreArticlesEnEtatNormal,
                    'nombreArticlesEnAlerteStock' => $nombreArticlesEnAlerteStock,
                    'nombreArticlesEnRuptureStock' => $nombreArticlesEnRuptureStock,
                    'categoriesAchat' => $categoriesAchat,
                    'categoriesVente' => $categoriesVente,
                    'articles' => $articles
        ));
    }

    public function mouvementsQteArticle($em, $id, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('sum(s.qte) as qte', 's.typeDoc as typeDoc')
                ->from('AppBundle:Stock', 's')
                ->leftjoin('s.article', 'a')
                ->where('s.article=:id ')
                ->setParameter('id', $id)
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', array('bl', 'facture', 'br', 'ca', 'avc'));
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->groupBy('s.typeDoc')
                ->orderBy('qte', 'DESC')
                ->getQuery()
                ->getResult();
        return $result;
    }

    public function mouvementsTtcArticle($em, $id, $typeDocs, $startDateCreation, $endDateCreation) {
        $result = $em->createQueryBuilder()
                ->select('sum(s.ttc) as ttc')
                ->from('AppBundle:Stock', 's')
                ->where('s.article=:id ')
                ->setParameter('id', $id)
                ->andWhere('s.typeDoc IN (:typeDoc)')
                ->setParameter('typeDoc', $typeDocs);
        if ($startDateCreation) {
            $result = $result->andWhere('s.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $startDateCreation);
        }
        if ($endDateCreation) {
            $result = $result->andWhere('s.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $endDateCreation);
        }
        $result = $result->getQuery()
                ->getSingleScalarResult();
        return $result;
    }

    /**
     * Show article stats.
     *
     * @Route("/{id}", name="articles_stats_show")
     * @Method("GET")
     */
    public function showAction(Request $request) {
        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->find($id);
        if (!$article) {
            $this->get('session')->getFlashBag()->add('info', 'Il faut sélectionner un article.');
            return $this->redirectToRoute('articles_stats');
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
        $mouvements = $this->mouvementsQteArticle($em, $id, $startDateCreation, $endDateCreation);
        $articleAchatTTC = $this->mouvementsTtcArticle($em, $id, array('br', 'ca'), $startDateCreation, $endDateCreation);
        $articleVenteTTC = $this->mouvementsTtcArticle($em, $id, array('bl', 'facture'), $startDateCreation, $endDateCreation);
        return $this->render('stats/article/show.html.twig', array(
                    'mouvements' => $mouvements,
                    'article' => $article,
                    'articleAchatTTC' => $articleAchatTTC,
                    'articleVenteTTC' => $articleVenteTTC,
                    'idArticle' => $id
        ));
    }

    /**
     * redirect
     *
     * @Route("/id", name="articles_stats_id")
     * @Method("GET")
     */
    public function statsAction(Request $request) {
        return $this->redirectToRoute('articles_stats_show', array('id' => $request->query->get('id')));
    }

}

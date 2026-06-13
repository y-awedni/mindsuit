<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Fiche client controller.
 *
 * @Route("fiche/fournisseur/vente/document")
 */
class TotauxFicheFournisseurController extends Controller {

    public function getAchatParDocumentQbByParametres($em, $code, $fournisseur, $startDateCreation, $endDateCreation) {
        $qb = $em->getRepository('AppBundle:Stock')
                ->createQueryBuilder('a')
                ->select('a.id', 'a.designation', 'a.typeDoc', 'sum(a.ttc) as ttc', 'br.regle as brregle', 'br.reste as brreste', 'a.dateCreation')
                ->leftJoin('a.bonReception', 'br');
        $qb->where('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);
        if ($code) {
            $qb->andWhere('a.designation like :code')->setParameter('code', '%' . $code . '%');
        }
        $dateFormat = $this->get('app.format_date');
        if ($startDateCreation) {
            $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $dateFormat->formatDate($startDateCreation));
        }
        if ($endDateCreation) {
            $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $dateFormat->formatDate($endDateCreation));
        }
        return $qb;
    }

    /**
     * @Route("/totaux/document/{code}/{fournisseur}/{startDateCreation}/{endDateCreation}", name="fiche_client_vente_document_totaux", methods={"GET"})
     */
    public function totauxParDocumentAction($code = null, $fournisseur = null, $startDateCreation = null, $endDateCreation = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAchatParDocumentQbByParametres($em, $code, $fournisseur, $startDateCreation, $endDateCreation);
        $qb->addGroupBy('a.designation');
        $entities = $qb->getQuery()->getResult();
        $total = 0;
        $regle = 0;
        $reste = 0;
        foreach ($entities as $entity) {
            $total += $entity['ttc'];
            $regle += $entity['brregle'];
            $reste += $entity['brreste'];
        }
        return $this->render('fiche/fournisseur/achatParDocumentTotaux.html.twig', array(
                    'total' => $total,
                    'regle' => $regle,
                    'reste' => $reste
        ));
    }

    public function getAchatParArticleQbByParametres($em, $code, $designation, $fournisseur) {
        $qb = $em->getRepository('AppBundle:Stock')
                ->createQueryBuilder('a')
                ->select('a.id', 'art.code as code', 'art.designation as designation', 'sum(a.qte) as qte', 'sum(a.ttc) as ttc')
                ->leftJoin('a.article', 'art');
        $qb->where('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);
        if ($designation) {
            $qb->andWhere('art.designation like :designation')->setParameter('designation', '%' . $designation . '%');
        }
        if ($code) {
            $qb->andWhere('art.code like :code')->setParameter('code', '%' . $code . '%');
        }
        return $qb;
    }

    /**
     * @Route("/totaux/article/{code}/{designation}/{fournisseur}", name="fiche_client_vente_article_totaux", methods={"GET"})
     */
    public function totauxParArticleAction($code = null, $designation = null, $fournisseur = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAchatParArticleQbByParametres($em, $code, $designation, $fournisseur);
        $qb->addGroupBy('a.article');
        $entities = $qb->getQuery()->getResult();
        $qte = 0;
        $ttc = 0;
        foreach ($entities as $entity) {
            $qte += $entity['qte'];
            $ttc += $entity['ttc'];
        }
        return $this->render('fiche/fournisseur/achatParArticleTotaux.html.twig', array(
                    'qte' => $qte,
                    'ttc' => $ttc
        ));
    }

    public function getReglementsQbByParametres($em, $designation, $startDateCreation, $endDateCreation, $fournisseur, $modeReglement) {
        $qb = $em->getRepository('AppBundle:Mouvement')
                ->createQueryBuilder('a')
                ->select('a.id', 'a.designation', 'a.typeDoc', 'a.ttc', 'a.modeReglement', 'a.dateEcheance', 'a.numDoc', 'a.etat', 'a.dateCreation');
        $qb->where('a.fournisseur = :fournisseur')->setParameter('fournisseur', $fournisseur);

        if ($designation) {
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $designation . '%');
        }
        if ($modeReglement and $modeReglement !== 'tous') {
            $qb->andWhere('a.modeReglement like :modeReglement')->setParameter('modeReglement', $modeReglement);
        }
        $dateFormat = $this->get('app.format_date');
        if ($startDateCreation) {
            $qb->andWhere('a.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $dateFormat->formatDate($startDateCreation));
        }
        if ($endDateCreation) {
            $qb->andWhere('a.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $dateFormat->formatDate($endDateCreation));
        }
        return $qb;
    }

    /**
     * @Route("/totaux/reglements/{designation}/{startDateCreation}/{endDateCreation}/{fournisseur}/{modeReglement}", name="fiche_client_vente_avoirs_totaux", methods={"GET"})
     */
    public function totauxReglementsAction($designation = null, $startDateCreation = null, $endDateCreation = null, $fournisseur = null, $modeReglement = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getReglementsQbByParametres($em, $designation, $startDateCreation, $endDateCreation, $fournisseur, $modeReglement);
        $entities = $qb->getQuery()->getResult();
        $totalEspece = 0;
        $totalCheque = 0;
        $totalTraite = 0;
        $totalBanque = 0;
        $totalAvoir = 0;
        $total = 0;
        foreach ($entities as $entity) {
            switch ($entity['modeReglement']) {
                case 'Espéce':
                    $totalEspece += $entity['ttc'];
                    break;
                case 'Chéque':
                    $totalCheque += $entity['ttc'];
                    break;
                case 'Traite':
                    $totalTraite += $entity['ttc'];
                    break;
                case 'Banque':
                    $totalBanque += $entity['ttc'];
                    break;
                case 'Avoir':
                    $totalAvoir += $entity['ttc'];
                    break;
            }
        }
        $total += $totalEspece + $totalCheque + $totalTraite + $totalBanque + $totalAvoir;
        return $this->render('fiche/fournisseur/reglementsTotaux.html.twig', array(
                    'totalEspece' => $totalEspece,
                    'totalCheque' => $totalCheque,
                    'totalTraite' => $totalTraite,
                    'totalBanque' => $totalBanque,
                    'totalAvoir' => $totalAvoir,
                    'total' => $total
        ));
    }

}

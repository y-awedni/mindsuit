<?php

namespace App\Controller;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Fiche client controller.
 *
 * @Route("fiche/client/vente/document")
 */
class TotauxFicheClientController extends BaseController {

    public function getVenteParDocumentQbByParametres($em, $code, $typeDoc, $client, $startDateCreation, $endDateCreation) {
        $qb = $em->getRepository('App\\Entity\\Stock')
                ->createQueryBuilder('a')
                ->select('a.id', 'a.designation', 'a.typeDoc', 'sum(a.ttc) as ttc', 'f.regle as fregle', 'bl.regle as blregle', 'f.reste as freste', 'bl.reste as blreste', 'a.dateCreation')
                ->leftJoin('a.facture', 'f')
                ->leftJoin('a.bonLivraison', 'bl');
        $qb->where('a.client = :client')->setParameter('client', $client);
        if ($code) {
            $qb->andWhere('a.designation like :code')->setParameter('code', '%' . $code . '%');
        }
        if ($typeDoc) {
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $typeDoc);
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
     * @Route("/totaux/document/{code}/{typeDoc}/{client}/{startDateCreation}/{endDateCreation}", name="fiche_client_vente_document_totaux", methods={"GET"})
     */
    public function totauxParDocumentAction($code = null, $typeDoc = null, $client = null, $startDateCreation = null, $endDateCreation = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getVenteParDocumentQbByParametres($em, $code, $typeDoc, $client, $startDateCreation, $endDateCreation);
        $qb->addGroupBy('a.designation');
        $entities = $qb->getQuery()->getResult();
        $total = 0;
        $regle = 0;
        $reste = 0;
        foreach ($entities as $entity) {
            $total += $entity['typeDoc'] === 'bl' ? $entity['ttc'] : $entity['ttc'] + 0.500;
            $regle += $entity['typeDoc'] == 'bl' ? $entity['blregle'] : $entity['fregle'];
            $reste += $entity['typeDoc'] == 'bl' ? $entity['blreste'] : $entity['freste'];
        }
        return $this->render('fiche/client/venteParDocumentTotaux.html.twig', array(
                    'total' => $total,
                    'regle' => $regle,
                    'reste' => $reste
        ));
    }

    public function getVenteParArticleQbByParametres($em, $code, $designation, $client) {
        $qb = $em->getRepository('App\\Entity\\Stock')
                ->createQueryBuilder('a')
                ->select('a.id', 'art.code as code', 'art.designation as designation', 'sum(a.qte) as qte', 'sum(a.ttc) as ttc')
                ->leftJoin('a.article', 'art');
        $qb->where('a.client = :client')->setParameter('client', $client);
        if ($designation) {
            $qb->andWhere('art.designation like :designation')->setParameter('designation', '%' . $designation . '%');
        }
        if ($code) {
            $qb->andWhere('art.code like :code')->setParameter('code', '%' . $code . '%');
        }
        return $qb;
    }

    /**
     * @Route("/totaux/article/{code}/{designation}/{client}", name="fiche_client_vente_article_totaux", methods={"GET"})
     */
    public function totauxParArticleAction($code = null, $designation = null, $client = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getVenteParArticleQbByParametres($em, $code, $designation, $client);
        $qb->addGroupBy('a.article');
        $entities = $qb->getQuery()->getResult();
        $qte = 0;
        $ttc = 0;
        foreach ($entities as $entity) {
            $qte += $entity['qte'];
            $ttc += $entity['ttc'];
        }
        return $this->render('fiche/client/venteParArticleTotaux.html.twig', array(
                    'qte' => $qte,
                    'ttc' => $ttc
        ));
    }

    public function getAvoirsQbByParametres($em, $codeAvoir, $startDateCreation, $endDateCreation, $client, $codeArticle, $designationArticle) {
        $qb = $em->getRepository('App\\Entity\\LigneFactureAvoir')
                ->createQueryBuilder('a')
                ->select('a.id', 'fav.code as favCode', 'art.code as artCode', 'a.designation', 'a.qte', 'a.ttc', 'a.stock', 'a.reglement', 'fav.dateCreation')
                ->leftJoin('a.article', 'art')
                ->leftJoin('a.factureAvoir', 'fav')
                ->leftJoin('fav.facture', 'fact');
        $qb->where('fact.client = :client')->setParameter('client', $client);
        if ($codeAvoir) {
            $qb->andWhere('fav.code like :codeAvoir')->setParameter('codeAvoir', '%' . $codeAvoir . '%');
        }
        if ($codeArticle) {
            $qb->andWhere('art.code like :codeArticle')->setParameter('codeArticle', '%' . $codeArticle . '%');
        }
        if ($designationArticle) {
            $qb->andWhere('a.designation like :designationArticle')->setParameter('designationArticle', '%' . $designationArticle . '%');
        }
        $dateFormat = $this->get('app.format_date');
        if ($startDateCreation) {
            $qb->andWhere('fav.dateCreation >= :startDateCreation')->setParameter('startDateCreation', $dateFormat->formatDate($startDateCreation));
        }
        if ($endDateCreation) {
            $qb->andWhere('fav.dateCreation <= :endDateCreation')->setParameter('endDateCreation', $dateFormat->formatDate($endDateCreation));
        }
        return $qb;
    }

    /**
     * @Route("/totaux/avoirs/{codeAvoir}/{startDateCreation}/{endDateCreation}/{client}/{codeArticle}/{designationArticle}", name="fiche_client_vente_avoirs_totaux", methods={"GET"})
     */
    public function totauxAvoirsAction($codeAvoir = null, $startDateCreation = null, $endDateCreation = null, $client = null, $codeArticle = null, $designationArticle = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getAvoirsQbByParametres($em, $codeAvoir, $startDateCreation, $endDateCreation, $client, $codeArticle, $designationArticle);
        $entities = $qb->getQuery()->getResult();
        $totalNonRembourse = 0;
        $totalRembourse = 0;
        $total = 0;
        foreach ($entities as $entity) {
            $entity['reglement'] ? $totalRembourse += $entity['ttc'] : $totalNonRembourse += $entity['ttc'];
        }
        $total += $totalNonRembourse + $totalRembourse;
        return $this->render('fiche/client/avoirsTotaux.html.twig', array(
                    'totalNonRembourse' => $totalNonRembourse,
                    'totalRembourse' => $totalRembourse,
                    'total' => $total
        ));
    }

    public function getReglementsQbByParametres($em, $designation, $startDateCreation, $endDateCreation, $client, $typeDoc, $modeReglement) {
        $qb = $em->getRepository('App\\Entity\\Mouvement')
                ->createQueryBuilder('a')
                ->select('a.id', 'a.designation', 'a.typeDoc', 'a.ttc', 'a.modeReglement', 'a.dateEcheance', 'a.numDoc', 'a.etat', 'a.dateCreation');
        $qb->where('a.client = :client')->setParameter('client', $client);

        if ($designation) {
            $qb->andWhere('a.designation like :designation')->setParameter('designation', '%' . $designation . '%');
        }
        if ($typeDoc) {
            $qb->andWhere('a.typeDoc like :typeDoc')->setParameter('typeDoc', $typeDoc);
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
     * @Route("/totaux/reglements/{designation}/{startDateCreation}/{endDateCreation}/{client}/{typeDoc}/{modeReglement}", name="fiche_client_vente_avoirs_totaux", methods={"GET"})
     */
    public function totauxReglementsAction($designation = null, $startDateCreation = null, $endDateCreation = null, $client = null, $typeDoc = null, $modeReglement = null) {
        $em = $this->getDoctrine()->getManager();
        $qb = $this->getReglementsQbByParametres($em, $designation, $startDateCreation, $endDateCreation, $client, $typeDoc, $modeReglement);
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
        $total += $totalEspece+$totalCheque+$totalTraite+$totalBanque+$totalAvoir;
        return $this->render('fiche/client/reglementsTotaux.html.twig', array(
                    'totalEspece' => $totalEspece,
                    'totalCheque' => $totalCheque,
                    'totalTraite' => $totalTraite,
                    'totalBanque' => $totalBanque,
                    'totalAvoir' => $totalAvoir,
                    'total' => $total
        ));
    }

}

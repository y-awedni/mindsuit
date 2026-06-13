<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $stocks = $em->getRepository('AppBundle:Stock')->findBy(array(), array('id' => 'DESC'), 10);
        $mouvements = $em->getRepository('AppBundle:Mouvement')->findBy(array(), array('id' => 'DESC'), 10);
        $countDevis = $em->getRepository('AppBundle:Devis')->findAllCount();
        $countFactures = $em->getRepository('AppBundle:Facture')->findAllCount();
        $countBls = $em->getRepository('AppBundle:BonLivraison')->findAllCount();
        $countBrs = $em->getRepository('AppBundle:BonReception')->findAllCount();
        $countAlertStock = $em->getRepository('AppBundle:Article')->findAllCountAlertStock();
        $countRuptureStock = $em->getRepository('AppBundle:Article')->findAllCountRuptureStock();

        $countFacturesEcheances0 = $em->getRepository('AppBundle:Facture')->findAllEcheanceByDateSys(0);
        $countFacturesEcheances1 = $em->getRepository('AppBundle:Facture')->findAllEcheanceByDateSys(1);
        $countFacturesEcheances2 = $em->getRepository('AppBundle:Facture')->findAllEcheanceByDateSys(2);
        $countFacturesEcheances3 = $em->getRepository('AppBundle:Facture')->findAllEcheanceByDateSys(3);

        $countChequeEntreeEcheances0 = $em->getRepository('AppBundle:Mouvement')->findAllCountChequeEntre(-1, null);
        $countChequeEntreeEcheances1 = $em->getRepository('AppBundle:Mouvement')->findAllCountChequeEntre(0, 0);
        $countChequeEntreeEcheances3 = $em->getRepository('AppBundle:Mouvement')->findAllCountChequeEntre(7, 1);

        $countTraiteEntreeEcheances0 = $em->getRepository('AppBundle:Mouvement')->findAllCountTraiteEntrants(-1, null);
        $countTraiteEntreeEcheances1 = $em->getRepository('AppBundle:Mouvement')->findAllCountTraiteEntrants(0, 0);
        $countTraiteEntreeEcheances3 = $em->getRepository('AppBundle:Mouvement')->findAllCountTraiteEntrants(7, 1);

        $countTraiteSortieEcheances0 = $em->getRepository('AppBundle:Mouvement')->findAllCountTraiteSortants(-1, null);
        $countTraiteSortieEcheances1 = $em->getRepository('AppBundle:Mouvement')->findAllCountTraiteSortants(0, 0);
        $countTraiteSortieEcheances3 = $em->getRepository('AppBundle:Mouvement')->findAllCountTraiteSortants(7, 1);

        $countChequeSortieEcheances0 = $em->getRepository('AppBundle:Mouvement')->findAllCountChequeSortie(-1, null);
        $countChequeSortieEcheances1 = $em->getRepository('AppBundle:Mouvement')->findAllCountChequeSortie(0, 0);
        $countChequeSortieEcheances3 = $em->getRepository('AppBundle:Mouvement')->findAllCountChequeSortie(7, 1);

        return $this->render('default/index.html.twig', [
                    'stocks' => $stocks,
                    'mouvements' => $mouvements,
                    'countDevis' => $countDevis,
                    'countFactures' => $countFactures,
                    'countBls' => $countBls,
                    'countBrs' => $countBrs,
                    'countAlertStock' => $countAlertStock,
                    'countRuptureStock' => $countRuptureStock,
                    'countFacturesEcheances0' => $countFacturesEcheances0,
                    'countFacturesEcheances1' => $countFacturesEcheances1,
                    'countFacturesEcheances2' => $countFacturesEcheances2,
                    'countFacturesEcheances3' => $countFacturesEcheances3,
                    'countChequeEntreeEcheances0' => $countChequeEntreeEcheances0,
                    'countChequeEntreeEcheances1' => $countChequeEntreeEcheances1,
                    'countChequeEntreeEcheances3' => $countChequeEntreeEcheances3,
                    'countChequeSortieEcheances0' => $countChequeSortieEcheances0,
                    'countChequeSortieEcheances1' => $countChequeSortieEcheances1,
                    'countChequeSortieEcheances3' => $countChequeSortieEcheances3,
                    'countTraiteEntreeEcheances0' => $countTraiteEntreeEcheances0,
                    'countTraiteEntreeEcheances1' => $countTraiteEntreeEcheances1,
                    'countTraiteEntreeEcheances3' => $countTraiteEntreeEcheances3,
                    'countTraiteSortieEcheances0' => $countTraiteSortieEcheances0,
                    'countTraiteSortieEcheances1' => $countTraiteSortieEcheances1,
                    'countTraiteSortieEcheances3' => $countTraiteSortieEcheances3
        ]);
    }

}

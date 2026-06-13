<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Controller\BaseController;

class DefaultController extends BaseController {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $stocks = $em->getRepository('App\\Entity\\Stock')->findBy(array(), array('id' => 'DESC'), 10);
        $mouvements = $em->getRepository('App\\Entity\\Mouvement')->findBy(array(), array('id' => 'DESC'), 10);
        $countDevis = $em->getRepository('App\\Entity\\Devis')->findAllCount();
        $countFactures = $em->getRepository('App\\Entity\\Facture')->findAllCount();
        $countBls = $em->getRepository('App\\Entity\\BonLivraison')->findAllCount();
        $countBrs = $em->getRepository('App\\Entity\\BonReception')->findAllCount();
        $countAlertStock = $em->getRepository('App\\Entity\\Article')->findAllCountAlertStock();
        $countRuptureStock = $em->getRepository('App\\Entity\\Article')->findAllCountRuptureStock();

        $countFacturesEcheances0 = $em->getRepository('App\\Entity\\Facture')->findAllEcheanceByDateSys(0);
        $countFacturesEcheances1 = $em->getRepository('App\\Entity\\Facture')->findAllEcheanceByDateSys(1);
        $countFacturesEcheances2 = $em->getRepository('App\\Entity\\Facture')->findAllEcheanceByDateSys(2);
        $countFacturesEcheances3 = $em->getRepository('App\\Entity\\Facture')->findAllEcheanceByDateSys(3);

        $countChequeEntreeEcheances0 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountChequeEntre(-1, null);
        $countChequeEntreeEcheances1 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountChequeEntre(0, 0);
        $countChequeEntreeEcheances3 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountChequeEntre(7, 1);

        $countTraiteEntreeEcheances0 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountTraiteEntrants(-1, null);
        $countTraiteEntreeEcheances1 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountTraiteEntrants(0, 0);
        $countTraiteEntreeEcheances3 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountTraiteEntrants(7, 1);

        $countTraiteSortieEcheances0 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountTraiteSortants(-1, null);
        $countTraiteSortieEcheances1 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountTraiteSortants(0, 0);
        $countTraiteSortieEcheances3 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountTraiteSortants(7, 1);

        $countChequeSortieEcheances0 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountChequeSortie(-1, null);
        $countChequeSortieEcheances1 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountChequeSortie(0, 0);
        $countChequeSortieEcheances3 = $em->getRepository('App\\Entity\\Mouvement')->findAllCountChequeSortie(7, 1);

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

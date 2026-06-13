<?php

// src/Repository/ProductRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class FactureRepository extends EntityRepository {

    public function findAllByYear($year) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(f.id) FROM App\\Entity\\Facture f WHERE f.createdAt BETWEEN '" . $min . "' AND '" . $max . "'"
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCount() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(f.id) FROM App\\Entity\\Facture f"
                        )
                        ->getSingleScalarResult();
    }
    
    public function findAllRevenusByInterval($d1,$d2) {
        $dateDebut=date_format($d1, "Y-m-d");
        $dateFin=date_format($d2, "Y-m-d");
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT f FROM App\\Entity\\Facture f WHERE f.dateCreation BETWEEN '" . $dateDebut . "' AND '" . $dateFin . "' ORDER BY f.dateCreation ASC"
                        )
                        ->getResult();
    }
    
    public function findAllEcheanceByDateSys($days){
        $format='Y-m-d';
        $date = date($format);
        $sys_date = new \DateTime($date);
        $sys_date->modify('+'.$days. " days");
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(f.id) FROM App\\Entity\\Facture f WHERE f.regle<f.total AND f.dateEcheance ='" . date_format($sys_date, $format) . "'"
                        )
                        ->getSingleScalarResult();
    }
    
    
    public function findAllTotalAvoirRemboursement($factureId){
        //somma des montant des mouvement
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT sum(lfa.ttc) FROM App\\Entity\\LigneFactureAvoir lfa "
                                . "JOIN lfa.factureAvoir fa "
                                . "JOIN fa.facture f "
                                . "where lfa.reglement=true "
                                . "and f.id=".$factureId
                        )
                        ->getSingleScalarResult();
    }
}

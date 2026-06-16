<?php

// src/Repository/ProductRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MouvementRepository extends EntityRepository {

    public function findAllDepencesByInterval($d1, $d2) {
        $dateDebut = date_format($d1, "Y-m-d");
        $dateFin = date_format($d2, "Y-m-d");

        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT m FROM App\\Entity\\Mouvement m WHERE m.mouvement LIKE 'depense' AND m.dateCreation BETWEEN '" . $dateDebut . "' AND '" . $dateFin . "' ORDER BY m.dateCreation ASC"
                        )
                        ->getResult();
    }

    public function findAllRevenusByInterval($d1, $d2) {
        $dateDebut = date_format($d1, "Y-m-d");
        $dateFin = date_format($d2, "Y-m-d");
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT m FROM App\\Entity\\Mouvement m WHERE m.mouvement LIKE 'revenu' AND m.dateCreation BETWEEN '" . $dateDebut . "' AND '" . $dateFin . "' ORDER BY m.dateCreation ASC"
                        )
                        ->getResult();
    }

    
    public function findAllCountChequeEntre($from, $towards) {
        if ($from!==null) {
            $from_date = new \DateTime(date('Y-m-d'));
            $from_date->modify('+' . $from . " days");
            $from=date_format($from_date, 'Y-m-d');
        }
        if ($towards!==null) {
            $towards_date = new \DateTime(date('Y-m-d'));
            $towards_date->modify('+' . $towards . " days");
            $towards=date_format($towards_date, 'Y-m-d');
        }
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(m.id) FROM App\\Entity\\Mouvement m "
                                . "where m.mouvement LIKE 'revenu' "
                                . "And m.etat IN ('En cours', 'Impayé')"
                                . "AND m.modeReglement LIKE 'Chéque' "
                                . ($towards !== null ? "AND m.dateEcheance >='{$towards}' " : "")
                                . ($from !== null ? "AND m.dateEcheance <='{$from}'" : "")
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCountTraiteEntrants($from, $towards) {
        if ($from!==null) {
            $from_date = new \DateTime(date('Y-m-d'));
            $from_date->modify('+' . $from . " days");
            $from=date_format($from_date, 'Y-m-d');
        }
        if ($towards!==null) {
            $towards_date = new \DateTime(date('Y-m-d'));
            $towards_date->modify('+' . $towards . " days");
            $towards=date_format($towards_date, 'Y-m-d');
        }
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(m.id) FROM App\\Entity\\Mouvement m "
                                . "where m.mouvement LIKE 'revenu' "
                                . "And m.etat IN ('En cours', 'Impayé')"
                                . "AND m.modeReglement LIKE 'Traite' "
                                . ($towards !== null ? "AND m.dateEcheance >='{$towards}' " : "")
                                . ($from !== null ? "AND m.dateEcheance <='{$from}'" : "")
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCountTraiteSortants($from, $towards) {
        if ($from!==null) {
            $from_date = new \DateTime(date('Y-m-d'));
            $from_date->modify('+' . $from . " days");
            $from=date_format($from_date, 'Y-m-d');
        }
        if ($towards!==null) {
            $towards_date = new \DateTime(date('Y-m-d'));
            $towards_date->modify('+' . $towards . " days");
            $towards=date_format($towards_date, 'Y-m-d');
        }
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(m.id) FROM App\\Entity\\Mouvement m "
                                . "where m.mouvement LIKE 'depense' "
                                . "And m.etat IN ('En cours', 'Impayé')"
                                . "AND m.modeReglement LIKE 'Traite' "
                                . ($towards !== null ? "AND m.dateEcheance >='{$towards}' " : "")
                                . ($from !== null ? "AND m.dateEcheance <='{$from}'" : "")
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCountChequeSortie($from, $towards) {
        if ($from!==null) {
            $from_date = new \DateTime(date('Y-m-d'));
            $from_date->modify('+' . $from . " days");
            $from=date_format($from_date, 'Y-m-d');
        }
        if ($towards!==null) {
            $towards_date = new \DateTime(date('Y-m-d'));
            $towards_date->modify('+' . $towards . " days");
            $towards=date_format($towards_date, 'Y-m-d');
        }
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(m.id) FROM App\\Entity\\Mouvement m "
                                . "where m.mouvement LIKE 'depense' "
                                . "And m.etat IN ('En cours', 'Impayé')"
                                . "AND m.modeReglement LIKE 'Chéque' "
                                . ($towards !== null ? "AND m.dateEcheance >='{$towards}' " : "")
                                . ($from !== null ? "AND m.dateEcheance <='{$from}'" : "")
                        )
                        ->getSingleScalarResult();
    }

}

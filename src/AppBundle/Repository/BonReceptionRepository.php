<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BonReceptionRepository extends EntityRepository {

    public function findAllByYear($year) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(br) FROM AppBundle:BonReception br WHERE br.createdAt BETWEEN '" . $min . "' AND '" . $max . "'"
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCount() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(br.id) FROM AppBundle:BonReception br"
                        )
                        ->getSingleScalarResult();
    }
    
    public function findAllDepencesByInterval($d1,$d2) {
        $dateDebut=date_format($d1, "Y-m-d");
        $dateFin=date_format($d2, "Y-m-d");
        
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT br FROM AppBundle:BonReception br WHERE br.dateReception BETWEEN '" . $dateDebut . "' AND '" . $dateFin . "' ORDER BY br.dateReception ASC"
                        )
                        ->getResult();
    }
}

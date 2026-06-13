<?php

// src/AppBundle/Repository/ProductRepository.php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FactureAvoirRepository extends EntityRepository {

    public function findAllByYear($year) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(f.id) FROM AppBundle:FactureAvoir f WHERE f.createdAt BETWEEN '" . $min . "' AND '" . $max . "'"
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCount() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(f.id) FROM AppBundle:FactureAvoir f"
                        )
                        ->getSingleScalarResult();
    }
    
    
}

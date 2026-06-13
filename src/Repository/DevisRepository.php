<?php

// src/Repository/ProductRepository.php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class DevisRepository extends EntityRepository {

    public function findAllByYear($year) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(d) FROM App\\Entity\\Devis d WHERE d.createdAt BETWEEN '" . $min . "' AND '" . $max . "'"
                        )
                        ->getSingleScalarResult();
    }
    
    public function findAllCount() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(d.id) FROM App\\Entity\\Devis d"
                        )
                        ->getSingleScalarResult();
    }

}

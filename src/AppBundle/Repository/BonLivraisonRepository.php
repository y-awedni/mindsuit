<?php

// src/AppBundle/Repository/ProductRepository.php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BonLivraisonRepository extends EntityRepository {

    public function findAllByYear($year) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(bl) FROM AppBundle:BonLivraison bl WHERE bl.createdAt BETWEEN '" . $min . "' AND '" . $max . "'"
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCount() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(bl.id) FROM AppBundle:BonLivraison bl"
                        )
                        ->getSingleScalarResult();
    }

}

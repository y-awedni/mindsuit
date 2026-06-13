<?php
namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;

/**
 * Description of BonCommandeFrsRepository
 *
 * @author lead_dev
 */
class BonCommandeFrsRepository extends EntityRepository{
    public function findAllByYear($year) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(bcf) FROM AppBundle:BonCommandeFrs bcf WHERE bcf.createdAt BETWEEN '" . $min . "' AND '" . $max . "'"
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCount() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(bcf.id) FROM AppBundle:BonCommandeFrs bcf"
                        )
                        ->getSingleScalarResult();
    }
}

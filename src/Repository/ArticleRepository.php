<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ArticleRepository
 *
 * @author lead_dev
 */
class ArticleRepository extends EntityRepository {

    public function findAllCountAlertStock() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(a.id) FROM App\\Entity\\Article a WHERE a.stockable=true and a.qteEnStock >0 and a.seuilAlert>a.qteEnStock"
                        )
                        ->getSingleScalarResult();
    }

    public function findAllCountRuptureStock() {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT count(a.id) FROM App\\Entity\\Article a WHERE a.stockable=true and a.qteEnStock is not NULL AND a.qteEnStock<=0"
                        )
                        ->getSingleScalarResult();
    }

    public function findByIdCodeAndId($id) {
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT a.id,a.code,a.prixVenteHt,a.designation,t.id,a.prixVenteTtc,a.stockable FROM App\\Entity\\Article a JOIN a.tva t WHERE a.id =".$id
                        )
                        ->getOneOrNullResult();
    }
    
    public function findAllServicesAndProduitsNonStockables(){
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT a FROM App\\Entity\\Article a WHERE a.service=1 OR a.service=0 AND a.stockable=0"
                        )
                        ->getResult();
    }
    
    public function findAllProduitsStockable(){
        return $this->getEntityManager()
                        ->createQuery(
                                "SELECT a FROM App\\Entity\\Article a WHERE a.service=0 AND a.stockable=1"
                        )
                        ->getResult();
    }

}

<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Devis;
use AppBundle\Entity\Facture;
use AppBundle\Entity\BonLivraison;
use AppBundle\Entity\BonReception;
use AppBundle\Entity\BonCommandeFrs;
use AppBundle\Entity\FactureAvoir;

class CodeGeneratorListener {

    public function getLastNumberDocument($entityName, $year, $em, $prefix) {
        $min = $year . '-01-01 00:00:00.000000';
        $max = $year . '-12-31 00:00:00.000000';
        $code = $em->getRepository('AppBundle:' . $entityName)
                ->createQueryBuilder('a')
                ->where('a.createdAt BETWEEN :min AND :max')
                ->setParameter('min', $min)
                ->setParameter('max', $max)
                ->select('a.code as code')
                ->orderBy('a.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        $initDoc = 1; //initial value of document
        switch ($prefix) {
            case 'F':
                $initDoc = 1;
                break;
            case 'BL':
                $initDoc = 1;
                break;
        }
        return $code ? explode('-', $code['code'])[2] + 1 : $initDoc;
    }

    public function getCode($args, $entityName, $prefix) {
        $em = $args->getEntityManager();
        $time = new \DateTime();
        $this_year = $time->format('Y');
        $_count = $this->getLastNumberDocument($entityName, $this_year, $em, $prefix);
        $count = str_pad($_count, 4, "0", STR_PAD_LEFT);
        return $prefix . '-' . $this_year . '-' . $count;
    }
    
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof Devis) {
            $entity->setCode($this->getCode($args, 'Devis', 'D'));
        }
        if ($entity instanceof Facture) {
            $entity->setCode($this->getCode($args, 'Facture', 'F'));
        }
        if ($entity instanceof FactureAvoir) {
            $entity->setCode($this->getCode($args, 'FactureAvoir', 'AVC'));
        }
        if ($entity instanceof BonLivraison) {
            $entity->setCode($this->getCode($args, 'BonLivraison', 'BL'));
        }
        if ($entity instanceof BonReception) {
            $entity->setCode($this->getCode($args, 'BonReception', 'BR'));
        }
        if ($entity instanceof BonCommandeFrs) {
            $entity->setCode($this->getCode($args, 'BonCommandeFrs', 'BCF'));
        }
        
    }

}

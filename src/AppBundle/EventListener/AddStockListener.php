<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use AppBundle\Entity\BonReception;
use AppBundle\Entity\Stock;
use AppBundle\Entity\Article;
use AppBundle\Entity\FactureAvoir;

class AddStockListener {

    public function addStock($em, $entity) {
        if ($entity instanceof BonReception) {
            foreach ($entity->getLigneBonReceptions() as $ligne) {
                $stock = new Stock();
                $stock->setDesignation($entity->getCode());
                $stock->setArticle($ligne->getArticle());
                $stock->setQte($ligne->getQte());
                $stock->setTypeDoc('br');
                $stock->setMouvement(true);
                $stock->setTtc($ligne->getTtc());
                $stock->setFournisseur($entity->getFournisseur());
                $stock->setDateCreation($entity->getDateReception());
                $em->persist($stock);
                $em->flush($stock);
            }
        } elseif ($entity instanceof FactureAvoir) {
            foreach ($entity->getLigneFactureAvoirs() as $ligne) {
                if ($ligne->getStock() and $ligne->getQte()>0) {
                    $stock = new Stock();
                    $stock->setDesignation($entity->getCode());
                    $stock->setArticle($ligne->getArticle());
                    $stock->setQte($ligne->getQte());
                    $stock->setTypeDoc('avc');
                    $stock->setMouvement(true);
                    $stock->setTtc($ligne->getTtc());
                    $stock->setClient($entity->getFacture()->getClient());
                    $stock->setDateCreation($entity->getDateCreation());
                    $em->persist($stock);
                    $em->flush($stock);
                }
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        switch (true) {
            case $entity instanceof BonReception:
                if ($entity->getTermine()) {
                    $em = $args->getObjectManager();
                    $this->addStock($em, $entity);
                }
                break;
            case $entity instanceof FactureAvoir:
                if ($entity->getTermine()) {
                    $em = $args->getObjectManager();
                    $this->addStock($em, $entity);
                }
                break;
            case $entity instanceof Article:
                if ($entity->getQteEnDepart()) {
                    $em = $args->getObjectManager();
                    $stock = new Stock();
                    $stock->setDesignation("Création d'article " . $entity->getCode());
                    $stock->setArticle($entity);
                    $stock->setQte($entity->getQteEnDepart());
                    $stock->setTypeDoc('ca');
                    $stock->setMouvement(true);
                    $stock->setTtc($entity->getPrixAchat());
                    $stock->setFournisseur($entity->getFournisseur());
                    $stock->setDateCreation($entity->getCreatedAt());
                    $em->persist($stock);
                    $em->flush($stock);
                }
                break;
        }
    }

    public $regleIsChanged = false;

    public function preUpdate(PreUpdateEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof BonReception or $entity instanceof FactureAvoir) {
            if ($args->hasChangedField('regle') or $args->hasChangedField('reste')) {
                $this->regleIsChanged = true;
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof BonReception or $entity instanceof FactureAvoir) {
            if ($entity->getTermine() and ! $this->regleIsChanged) {
                $em = $args->getObjectManager();
                $this->addStock($em, $entity);
            }
        }
    }

}

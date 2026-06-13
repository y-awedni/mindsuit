<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\BonLivraison;
use App\Entity\Facture;
use App\Entity\Stock;

class RemoveStockListener {

    public function removeStock($em, $entity, $ligne, $type) {
        $stock = new Stock();
        $stock->setDesignation($entity->getCode());
        $stock->setArticle($ligne->getArticle());
        $stock->setQte($ligne->getQte());
        $stock->setTypeDoc($type);
        $stock->setMouvement(false);
        $stock->setTtc($ligne->getTtc());
        $stock->setClient($entity->getClient());
        $type === 'bl' ? $stock->setDateCreation($entity->getDateLivraison()) : $stock->setDateCreation($entity->getDateCreation());
        $em->persist($stock);
        $em->flush($stock);
    }

    public function removeStockBl($em, $entity) {
        foreach ($entity->getLigneBonLivraisons() as $ligne) {
            $this->removeStock($em, $entity, $ligne, 'bl');
        }
    }

    public function removeStockFacture($em, $entity) {
        foreach ($entity->getLignesFactures() as $ligne) {
            $this->removeStock($em, $entity, $ligne, 'facture');
        }
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof BonLivraison) {
            if ($entity->getTermine()) {
                $em = $args->getObjectManager();
                $this->removeStockBl($em, $entity);
            }
            return;
        }
        if ($entity instanceof Facture) {
            if ($entity->getTermine() and ! $entity->getFromBl()) {
                $em = $args->getObjectManager();
                $this->removeStockFacture($em, $entity);
            }
            return;
        }
    }

    public $regleBLIsChanged = false;
    public $regleFactureIsChanged = false;

    public function preUpdate(PreUpdateEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof BonLivraison) {
            if ($args->hasChangedField('regle')) {
                $this->regleBLIsChanged = true;
            }
            return;
        }
        if ($entity instanceof Facture) {
            if ($args->hasChangedField('regle') or $args->hasChangedField('reste')) {
                $this->regleFactureIsChanged = true;
            }
            return;
        }
    }

    public function deleteAncienStockAndAddQteArticle($em, $entity) {
        $stocks = $em->getRepository('App\\Entity\\Stock')->findByDesignation($entity->getCode());
        foreach ($stocks as $ligne) {
            $qteEnStock = $ligne->getArticle()->getQteEnStock();
            $qteAajouter = $ligne->getQte();
            $article = $em->getRepository('App\\Entity\\Article')->findOneById($ligne->getArticle()->getId());
            $article->setQteEnStock($qteEnStock + $qteAajouter); //ajout du stock en article
            $em->flush($article);
            $em->remove($ligne);
            $em->flush();
        }
    }

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof BonLivraison) {
            if ($entity->getTermine() and ! $entity->getConverted() and ! $this->regleBLIsChanged) {
                $em = $args->getObjectManager();
                $this->deleteAncienStockAndAddQteArticle($em, $entity);
                $this->removeStockBl($em, $entity);
            }
            return;
        }
        if ($entity instanceof Facture) {
            if ($entity->getTermine() and ! $entity->getFromBl() and ! $this->regleFactureIsChanged) {
                $em = $args->getObjectManager();
                $this->removeStockFacture($em, $entity);
            }
            return;
        }
    }

}

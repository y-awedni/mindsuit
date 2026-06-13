<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\BonReception;
use App\Entity\LigneReglementBonReception;
use App\Entity\BonLivraison;
use App\Entity\LigneReglementBonLivraison;
use App\Entity\Facture;
use App\Entity\LigneReglement;
use App\Entity\Mouvement;

class RetenuAlaSourceListener {

    public function addReglement($em, $entity) {
        if ($entity->getTotalRetenu() > 0) {
            switch (true) {
                case $entity instanceof BonReception:
                    $reglement = new LigneReglementBonReception();
                    $reglement->setType('reglement');
                    $reglement->setBonReception($entity);
                    $reglement->setModeReglement('Retenu à la source');
                    $reglement->setMontant($entity->getTotalRetenu());
                    $reglement->setDateReglement($entity->getDateReception());
                    $em->persist($reglement);
                    $em->flush($reglement);
                    break;
                case $entity instanceof BonLivraison:
                    $reglement = new LigneReglementBonLivraison();
                    $reglement->setType('reglement');
                    $reglement->setBonLivraison($entity);
                    $reglement->setModeReglement('Retenu à la source');
                    $reglement->setMontant($entity->getTotalRetenu());
                    $reglement->setDateReglement($entity->getDateCreation());
                    $em->persist($reglement);
                    $em->flush($reglement);
                    break;
                case $entity instanceof Facture:
                    $reglement = new LigneReglement();
                    $reglement->setType('reglement');
                    $reglement->setFacture($entity);
                    $reglement->setModeReglement('Retenu à la source');
                    $reglement->setMontant($entity->getTotalRetenu());
                    $reglement->setDateReglement($entity->getDateCreation());
                    $em->persist($reglement);
                    $em->flush($reglement);
                    break;
            }
        }
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $em = $args->getObjectManager();
        if (($entity instanceof BonReception or $entity instanceof Facture or $entity instanceof BonLivraison) and $entity->getTermine() and $entity->getTotalRetenu() > 0) {
            return $this->addReglement($em, $entity);
        }
        if ($entity instanceof Mouvement and $entity->getTotalRetenu() > 0) {
            $em = $args->getObjectManager();
            $mouvement = new Mouvement();
            $mouvement->setDesignation($entity->getDesignation());
            $mouvement->setTier($entity->getTier());
            $mouvement->setTypeDoc($entity->getTypeDoc());
            $mouvement->setMouvement($entity->getMouvement());
            $mouvement->setTtc($entity->getTotalRetenu());
            $mouvement->setDateCreation($entity->getDateCreation());
            $mouvement->setModeReglement('Retenu à la source');
            $em->persist($mouvement);
            $em->flush($mouvement);
        }
    }

    public $regleIsChanged = false;

    public function preUpdate(PreUpdateEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof BonReception or $entity instanceof Facture or $entity instanceof BonLivraison) {
            if ($args->hasChangedField('regle') or $args->hasChangedField('reste')) {
                $this->regleIsChanged = true;
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        $em = $args->getObjectManager();
        if ($entity instanceof BonReception or $entity instanceof Facture or $entity instanceof BonLivraison and $entity->getTotalRetenu() > 0) {
            if ($entity->getTermine() and ! $this->regleIsChanged) {
                return $this->addReglement($em, $entity);
            }
        }
    }

}

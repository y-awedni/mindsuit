<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\LigneReglementBonReception;

class SetRegleBonReceptionListener {
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof LigneReglementBonReception){
            return;
        }
        $entityManager = $args->getObjectManager();
        $bonreception=$entity->getBonReception();
        $regle= $bonreception->getRegle();
        $bonreception->setRegle($regle+$entity->getMontant());
        $bonreception->setReste($bonreception->getTotal()-$bonreception->getRegle());
        $entityManager->flush();
    }
    
    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof LigneReglementBonReception){
            return;
        }
        $entityManager = $args->getObjectManager();
        $bonreception=$entity->getBonReception();
        $regle= $bonreception->getRegle();
        $bonreception->setRegle($regle-$entity->getMontant());
        $bonreception->setReste($bonreception->getTotal()- $bonreception->getRegle());
        $entityManager->flush();
    }
}

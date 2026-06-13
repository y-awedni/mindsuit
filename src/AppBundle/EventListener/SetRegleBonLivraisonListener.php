<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\LigneReglementBonLivraison;

class SetRegleBonLivraisonListener {
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof LigneReglementBonLivraison){
            return;
        }
        $entityManager = $args->getObjectManager();
        $bonlivraison=$entity->getBonLivraison();
        $regle= $bonlivraison->getRegle();
        $bonlivraison->setRegle($regle+$entity->getMontant());
        $bonlivraison->setReste($bonlivraison->getTotal()-$bonlivraison->getRegle());
        $entityManager->flush();
    }
    
    
    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof LigneReglementBonLivraison){
            return;
        }
        $entityManager = $args->getObjectManager();
        $bonlivraison=$entity->getBonLivraison();
        $regle= $bonlivraison->getRegle();
        $bonlivraison->setRegle($regle-$entity->getMontant());
        $bonlivraison->setReste($bonlivraison->getTotal()-$bonlivraison->getRegle());
        $entityManager->flush();
    }
}

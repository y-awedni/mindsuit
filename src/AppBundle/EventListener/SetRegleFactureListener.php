<?php
namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\LigneReglement;

class SetRegleFactureListener {
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof LigneReglement){
            return;
        }
        $entityManager = $args->getObjectManager();
        $facture=$entity->getFacture();
        $regle= $facture->getRegle();
        $facture->setRegle($regle+$entity->getMontant());
        $facture->setReste($facture->getTotal()-$facture->getRegle());
        
        if($entity->getModeReglement()==='Avoir'){
            $client=$facture->getClient();
            $client->setTotalAvoirNonRembourse($client->getTotalAvoirNonRembourse()-$entity->getMontant());
        }
        $entityManager->flush();
    }
    
    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if(!$entity instanceof LigneReglement){
            return;
        }
        $entityManager = $args->getObjectManager();
        $facture=$entity->getFacture();
        $regle= $facture->getRegle();
        $facture->setRegle($regle-$entity->getMontant());
        
        if($entity->getModeReglement()==='Avoir'){
            $client=$facture->getClient();
            $client->setTotalAvoirNonRembourse($client->getTotalAvoirNonRembourse()+$entity->getMontant());
        }
        $facture->setReste($facture->getTotal()-$facture->getRegle());
        $entityManager->flush();
    }
}

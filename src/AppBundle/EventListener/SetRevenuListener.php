<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\LigneReglement;
use AppBundle\Entity\LigneReglementBonLivraison;
use AppBundle\Entity\Mouvement;

class SetRevenuListener {

    public function addMouvement($mouvement, $typeDoc, $entity, $args) {
        $em = $args->getObjectManager();
        $mouvement->setTypeDoc($typeDoc);
        $mouvement->setMouvement('revenu');
        $mouvement->setTtc($entity->getMontant());
        
        $mouvement->setDateCreation($entity->getDateReglement());
        $mouvement->setModeReglement($entity->getModeReglement());
        
        if($entity->getModeReglement()==='Chéque'){
            $mouvement->setEtat('En cours');
            $mouvement->setDateEcheance($entity->getDateEcheanceCheque());
            $mouvement->setNumDoc($entity->getNumCheque());
        }elseif ($entity->getModeReglement()==='Traite') {
            $mouvement->setEtat('En cours');
            $mouvement->setDateEcheance($entity->getDateEcheanceTraite());
            $mouvement->setNumDoc($entity->getNumTraite());
        }
        $mouvement->setReglementId($entity->getId());
        $mouvement->setCompte($entity->getCompte());
        $em->persist($mouvement);
        $em->flush($mouvement);
    }

    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof LigneReglement) {
            $mouvement = new Mouvement();
            $mouvement->setDesignation($entity->getFacture()->getCode());
            $mouvement->setClient($entity->getFacture()->getClient());
            $this->addMouvement($mouvement, 'facture', $entity, $args);
            return;
        }
        if ($entity instanceof LigneReglementBonLivraison) {
            $mouvement = new Mouvement();
            $mouvement->setDesignation($entity->getBonLivraison()->getCode());
            $mouvement->setClient($entity->getBonLivraison()->getClient());
            $this->addMouvement($mouvement, 'bl', $entity, $args);
        }
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof LigneReglement or $entity instanceof LigneReglementBonLivraison) {
            $em = $args->getObjectManager();
            $mouvement = $em->getRepository('AppBundle:Mouvement')->findOneByReglementId($entity->getId());
            $em->remove($mouvement);
            $em->flush($mouvement);
        }
    }
}

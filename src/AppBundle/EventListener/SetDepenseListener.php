<?php
namespace AppBundle\EventListener;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\LigneReglementBonReception;
use AppBundle\Entity\Mouvement;

class SetDepenseListener {
    public function addMouvement($mouvement, $typeDoc, $entity, $args) {
        $em = $args->getObjectManager();
        $mouvement->setTypeDoc($typeDoc);
        $mouvement->setMouvement('depense');
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
        if ($entity instanceof LigneReglementBonReception) {
            $mouvement = new Mouvement();
            $mouvement->setDesignation($entity->getBonReception()->getCode());
            $mouvement->setFournisseur($entity->getBonReception()->getFournisseur());
            $this->addMouvement($mouvement, 'br', $entity, $args);
        }
    }

    public function preRemove(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if ($entity instanceof LigneReglementBonReception) {
            $em = $args->getObjectManager();
            $mouvement = $em->getRepository('AppBundle:Mouvement')->findOneByReglementId($entity->getId());
            $em->remove($mouvement);
            $em->flush($mouvement);
        }
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Mouvement
 *
 * @ORM\Table(name="mouvement", uniqueConstraints={@ORM\UniqueConstraint(name="reglement_id", columns={"reglement_id"})}, indexes={@ORM\Index(name="client_id", columns={"client_id", "fournisseur_id", "updated_user_id", "created_user_id"}), @ORM\Index(name="fournisseur_id", columns={"fournisseur_id"}), @ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="IDX_5B51FC3E19EB6921", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MouvementRepository")
 * @UniqueEntity("reglementId")
 */
class Mouvement {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable=false)
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="mouvement", type="string", length=255, nullable=false)
     */
    private $mouvement;

    /**
     * @var string
     *
     * @ORM\Column(name="type_doc", type="string", length=255, nullable=true)
     */
    private $typeDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="tier", type="string", length=255, nullable=true)
     */
    private $tier;

    /**
     * @var string
     *
     * @ORM\Column(name="ttc", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $ttc;
    
    /**
     * @var string
     *
     * @ORM\Column(name="total_retenu", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $totalRetenu;

    /**
     * @var integer
     *
     * @ORM\Column(name="reglement_id", type="integer", nullable=true)
     */
    private $reglementId;

    /**
     * @var string
     *
     * @ORM\Column(name="mode_reglement", type="string", length=255, nullable=true)
     */
    private $modeReglement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance", type="date", nullable=true)
     */
    private $dateEcheance;
    
    /**
     * @var string
     *
     * @ORM\Column(name="num_doc", type="string", length=255, nullable=true)
     */
    private $numDoc;
    
    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255, nullable=true)
     */
    private $etat;
    
    /**
     * @var \Compte
     *
     * @ORM\ManyToOne(targetEntity="Compte")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_id", referencedColumnName="id")
     * })
     */
    private $compte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     */
    private $dateCreation;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var \Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;

    /**
     * @var \Fournisseur
     *
     * @ORM\ManyToOne(targetEntity="Fournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fournisseur_id", referencedColumnName="id")
     * })
     */
    private $fournisseur;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id")
     * })
     */
    private $updatedUser;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_user_id", referencedColumnName="id")
     * })
     */
    private $createdUser;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set designation
     *
     * @param string $designation
     *
     * @return Mouvement
     */
    public function setDesignation($designation) {
        $this->designation = $designation;

        return $this;
    }

    /**
     * Get designation
     *
     * @return string
     */
    public function getDesignation() {
        return $this->designation;
    }

    /**
     * Set mouvement
     *
     * @param string $mouvement
     *
     * @return Mouvement
     */
    public function setMouvement($mouvement) {
        $this->mouvement = $mouvement;

        return $this;
    }

    /**
     * Get mouvement
     *
     * @return string
     */
    public function getMouvement() {
        return $this->mouvement;
    }

    /**
     * Set typeDoc
     *
     * @param string $typeDoc
     *
     * @return Mouvement
     */
    public function setTypeDoc($typeDoc) {
        $this->typeDoc = $typeDoc;

        return $this;
    }

    /**
     * Get typeDoc
     *
     * @return string
     */
    public function getTypeDoc() {
        return $this->typeDoc;
    }

    /**
     * Set tier
     *
     * @param string $tier
     *
     * @return Mouvement
     */
    public function setTier($tier) {
        $this->tier = $tier;

        return $this;
    }

    /**
     * Get tier
     *
     * @return string
     */
    public function getTier() {
        if ($this->tier) {
            return $this->tier;
        } else {
            return $this->fournisseur ? $this->fournisseur : $this->client;
        }
    }

    /**
     * Set ttc
     *
     * @param string $ttc
     *
     * @return Mouvement
     */
    public function setTtc($ttc) {
        $this->ttc = $ttc;

        return $this;
    }

    /**
     * Get ttc
     *
     * @return string
     */
    public function getTtc() {
        return $this->ttc;
    }
    
    /**
     * Set totalRetenu
     *
     * @param string $totalRetenu
     *
     * @return Mouvement
     */
    public function setTotalRetenu($totalRetenu) {
        $this->totalRetenu = $totalRetenu;

        return $this;
    }

    /**
     * Get totalRetenu
     *
     * @return string
     */
    public function getTotalRetenu() {
        return $this->totalRetenu;
    }

    /**
     * Set reglementId
     *
     * @param integer $reglementId
     *
     * @return Mouvement
     */
    public function setReglementId($reglementId) {
        $this->reglementId = $reglementId;

        return $this;
    }

    /**
     * Get reglementId
     *
     * @return integer
     */
    public function getReglementId() {
        return $this->reglementId;
    }

    /**
     * Set modeReglement
     *
     * @param string $modeReglement
     *
     * @return Mouvement
     */
    public function setModeReglement($modeReglement) {
        $this->modeReglement = $modeReglement;

        return $this;
    }

    /**
     * Get modeReglement
     *
     * @return string
     */
    public function getModeReglement() {
        return $this->modeReglement;
    }

    /**
     * Set dateEcheance
     *
     * @param \DateTime $dateEcheance
     *
     * @return Mouvement
     */
    public function setDateEcheance($dateEcheance) {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    /**
     * Get dateEcheance
     *
     * @return \DateTime
     */
    public function getDateEcheance() {
        return $this->dateEcheance;
    }
    
    /**
     * Set numDoc
     *
     * @param string $numDoc
     *
     * @return Mouvement
     */
    public function setNumDoc($numDoc) {
        $this->numDoc = $numDoc;

        return $this;
    }

    /**
     * Get numDoc
     *
     * @return string
     */
    public function getNumDoc() {
        return $this->numDoc;
    }
    
    /**
     * Set etat
     *
     * @param string $etat
     *
     * @return Mouvement
     */
    public function setEtat($etat) {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat() {
        return $this->etat;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Mouvement
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Mouvement
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Mouvement
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Mouvement
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Mouvement
     */
    public function setClient(\AppBundle\Entity\Client $client = null) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Client
     */
    public function getClient() {
        return $this->client;
    }
    
    /**
     * Set compte
     *
     * @param \AppBundle\Entity\Compte $compte
     *
     * @return Mouvement
     */
    public function setCompte(\AppBundle\Entity\Compte $compte = null) {
        $this->compte = $compte;

        return $this;
    }

    /**
     * Get compte
     *
     * @return \AppBundle\Entity\Compte
     */
    public function getCompte() {
        return $this->compte;
    }

    /**
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return Mouvement
     */
    public function setFournisseur(\AppBundle\Entity\Fournisseur $fournisseur = null) {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    /**
     * Get fournisseur
     *
     * @return \AppBundle\Entity\Fournisseur
     */
    public function getFournisseur() {
        return $this->fournisseur;
    }

    /**
     * Set updatedUser
     *
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return Mouvement
     */
    public function setUpdatedUser(\AppBundle\Entity\User $updatedUser = null) {
        $this->updatedUser = $updatedUser;

        return $this;
    }

    /**
     * Get updatedUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getUpdatedUser() {
        return $this->updatedUser;
    }

    /**
     * Set createdUser
     *
     * @param \AppBundle\Entity\User $createdUser
     *
     * @return Mouvement
     */
    public function setCreatedUser(\AppBundle\Entity\User $createdUser = null) {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * Get createdUser
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedUser() {
        return $this->createdUser;
    }

    public function __toString() {
        return $this->id;
    }
    
    public function __construct() {
        $this->dateCreation=new \DateTime();
    }

}

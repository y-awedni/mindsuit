<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Stock
 *
 * @ORM\Table(name="stock", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="updated_user_id_2", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="article_id", columns={"article_id"}), @ORM\Index(name="fournisseur_id", columns={"tier_id"})})
 * @ORM\Entity
 */
class Stock {

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
     * @ORM\Column(name="type_doc", type="string", length=255, nullable=false)
     */
    private $typeDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="qte", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\GreaterThan(0)
     */
    private $qte;

    /**
     * @var string
     *
     * @ORM\Column(name="ttc", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $ttc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mouvement", type="boolean", nullable=false)
     */
    private $mouvement;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     */
    private $dateCreation;

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
     * @var \Article
     *
     * @ORM\ManyToOne(targetEntity="Article")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="article_id", referencedColumnName="id")
     * })
     * @Assert\NotNull()
     */
    private $article;
    
    /**
     * @var \Facture
     *
     * @ORM\ManyToOne(targetEntity="Facture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     * })
     */
    private $facture;
    
    /**
     * @var \BonLivraison
     *
     * @ORM\ManyToOne(targetEntity="BonLivraison")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bon_livraison_id", referencedColumnName="id")
     * })
     */
    private $bonLivraison;
    
    /**
     * @var \FactureAvoir
     *
     * @ORM\ManyToOne(targetEntity="FactureAvoir")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="facture_avoir_id", referencedColumnName="id")
     * })
     */
    private $factureAvoir;
    
    /**
     * @var \BonReception
     *
     * @ORM\ManyToOne(targetEntity="BonReception")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bon_reception_id", referencedColumnName="id")
     * })
     */
    private $bonReception;

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
     * @return Stock
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
     * Set typeDoc
     *
     * @param string $typeDoc
     *
     * @return Stock
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
     * Set qte
     *
     * @param string $qte
     *
     * @return Stock
     */
    public function setQte($qte) {
        $this->qte = $qte;

        return $this;
    }

    /**
     * Get qte
     *
     * @return string
     */
    public function getQte() {
        return $this->qte;
    }

    /**
     * Set ttc
     *
     * @param string $ttc
     *
     * @return Stock
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
     * Set mouvement
     *
     * @param boolean $mouvement
     *
     * @return Stock
     */
    public function setMouvement($mouvement) {
        $this->mouvement = $mouvement;

        return $this;
    }

    /**
     * Get mouvement
     *
     * @return boolean
     */
    public function getMouvement() {
        return $this->mouvement;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Stock
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Stock
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
     * @return Stock
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
     * Set article
     *
     * @param \AppBundle\Entity\Article $article
     *
     * @return Stock
     */
    public function setArticle(\AppBundle\Entity\Article $article = null) {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \AppBundle\Entity\Article
     */
    public function getArticle() {
        return $this->article;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Client $client
     *
     * @return Stock
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
     * Set facture
     *
     * @param \AppBundle\Entity\Facture $facture
     *
     * @return Stock
     */
    public function setFacture(\AppBundle\Entity\Facture $facture = null) {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \AppBundle\Entity\Facture
     */
    public function getFacture() {
        return $this->facture;
    }
    
    /**
     * Set bonLivraison
     *
     * @param \AppBundle\Entity\BonLivraison $bonLivraison
     *
     * @return Stock
     */
    public function setBonLivraison(\AppBundle\Entity\BonLivraison $bonLivraison = null) {
        $this->bonLivraison = $bonLivraison;

        return $this;
    }

    /**
     * Get bonLivraison
     *
     * @return \AppBundle\Entity\BonLivraison
     */
    public function getBonLivraison() {
        return $this->bonLivraison;
    }
    
    /**
     * Set bonReception
     *
     * @param \AppBundle\Entity\BonReception $bonReception
     *
     * @return Stock
     */
    public function setBonReception(\AppBundle\Entity\BonReception $bonReception = null) {
        $this->bonReception = $bonReception;

        return $this;
    }

    /**
     * Get bonReception
     *
     * @return \AppBundle\Entity\BonReception
     */
    public function getBonReception() {
        return $this->bonReception;
    }
    
    /**
     * Set factureAvoir
     *
     * @param \AppBundle\Entity\FactureAvoir $factureAvoir
     *
     * @return Stock
     */
    public function setFactureAvoir(\AppBundle\Entity\FactureAvoir $factureAvoir = null) {
        $this->factureAvoir = $factureAvoir;

        return $this;
    }

    /**
     * Get factureAvoir
     *
     * @return \AppBundle\Entity\FactureAvoir
     */
    public function getFactureAvoir() {
        return $this->factureAvoir;
    }

    /**
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return Stock
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
     * @return Stock
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
     * @return Stock
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
        return "" . $this->id;
    }
    
    public function getTier() {
        return $this->fournisseur ? $this->fournisseur : $this->client;
    }

}

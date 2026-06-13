<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * LigneFactureAvoir
 *
 * @ORM\Table(name="ligne_facture_avoir", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="article_id", columns={"article_id"}), @ORM\Index(name="tva_id", columns={"tva_id"}), @ORM\Index(name="facture_avoir_id", columns={"facture_avoir_id"})})
 * @ORM\Entity
 */
class LigneFactureAvoir {
    
    
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
     * @ORM\Column(name="qte", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\Type("numeric")
     * 
     */
    private $qte;
    
    /**
     * @var string
     *
     * @ORM\Column(name="qte_max", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $qteMax;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_unitaire", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $prixUnitaire;

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $remise;

    /**
     * @var string
     *
     * @ORM\Column(name="ttc", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $ttc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stock", type="boolean", nullable=false)
     */
    private $stock = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reglement", type="boolean", nullable=false)
     */
    private $reglement = false;

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
     */
    private $article;

    /**
     * @var \Tva
     *
     * @ORM\ManyToOne(targetEntity="Tva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_id", referencedColumnName="id")
     * })
     */
    private $tva;

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
     * @var \FactureAvoir
     *
     * @ORM\ManyToOne(targetEntity="FactureAvoir",inversedBy="ligneFactureAvoirs",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="facture_avoir_id", referencedColumnName="id")
     * })
     */
    private $factureAvoir;

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
     * @return LigneFactureAvoir
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
     * Set qte
     *
     * @param string $qte
     *
     * @return LigneFactureAvoir
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
     * Set qte_max
     *
     * @param string $qteMax
     *
     * @return LigneFactureAvoir
     */
    public function setQteMax($qteMax) {
        $this->qteMax = $qteMax;

        return $this;
    }

    /**
     * Get qteMax
     *
     * @return string
     */
    public function getQteMax() {
        return $this->qteMax;
    }

    /**
     * Set prixUnitaire
     *
     * @param string $prixUnitaire
     *
     * @return LigneFactureAvoir
     */
    public function setPrixUnitaire($prixUnitaire) {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    /**
     * Get prixUnitaire
     *
     * @return string
     */
    public function getPrixUnitaire() {
        return $this->prixUnitaire;
    }

    /**
     * Set remise
     *
     * @param string $remise
     *
     * @return LigneFactureAvoir
     */
    public function setRemise($remise) {
        $this->remise = $remise;

        return $this;
    }

    /**
     * Get remise
     *
     * @return string
     */
    public function getRemise() {
        return $this->remise;
    }

    /**
     * Set ttc
     *
     * @param string $ttc
     *
     * @return LigneFactureAvoir
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
     * Set stock
     *
     * @param string $stock
     *
     * @return LigneFactureAvoir
     */
    public function setStock($stock) {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return string
     */
    public function getStock() {
        return $this->stock;
    }
    
    /**
     * Set reglement
     *
     * @param string $reglement
     *
     * @return LigneFactureAvoir
     */
    public function setReglement($reglement) {
        $this->reglement = $reglement;

        return $this;
    }

    /**
     * Get reglement
     *
     * @return string
     */
    public function getReglement() {
        return $this->reglement;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return LigneFactureAvoir
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
     * @return LigneFactureAvoir
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
     * @return LigneFactureAvoir
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
     * Set tva
     *
     * @param \AppBundle\Entity\Tva $tva
     *
     * @return LigneFactureAvoir
     */
    public function setTva(\AppBundle\Entity\Tva $tva = null) {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return \AppBundle\Entity\Tva
     */
    public function getTva() {
        return $this->tva;
    }

    /**
     * Set updatedUser
     *
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return LigneFactureAvoir
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
     * @return LigneFactureAvoir
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

    /**
     * Set factureAvoir
     *
     * @param \AppBundle\Entity\FactureAvoir $factureAvoir
     *
     * @return LigneFactureAvoir
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
    
    
}

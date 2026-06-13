<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LigneDevis
 *
 * @ORM\Table(name="ligne_devis", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="article_id", columns={"article_id"}), @ORM\Index(name="tva_id", columns={"tva_id"})})
 * @ORM\Entity
 */
class LigneDevis {

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
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $designation;
    
    /**
     * @var string
     *
     * @ORM\Column(name="qte", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     * 
     * @Assert\Type("int")
     *
     */
    private $qte;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_unitaire", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $prixUnitaire;

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal",  nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     * )
     */
    private $remise = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="ttc", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $ttc= '0.000';

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
     * @var \Tva
     *
     * @ORM\ManyToOne(targetEntity="Tva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
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
     * @var \Devis
     *
     * @ORM\ManyToOne(targetEntity="Devis",inversedBy="lignesDevis",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devis_id", referencedColumnName="id")
     * })
     */
    private $devis;

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
     * @return Article
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
     * @return LigneDevis
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
     * Set prixUnitaire
     *
     * @param string $prixUnitaire
     *
     * @return LigneDevis
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
     * @return LigneDevis
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
     * @return LigneDevis
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return LigneDevis
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
     * @return LigneDevis
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
     * @return LigneDevis
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
     * @return LigneDevis
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
     * @return LigneDevis
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
     * @return LigneDevis
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
     * Set devis
     *
     * @param \AppBundle\Entity\Devis $devis
     *
     * @return LigneDevis
     */
    public function setDevis(\AppBundle\Entity\Devis $devis = null) {
        $this->devis = $devis;

        return $this;
    }

    /**
     * Get devis
     *
     * @return \AppBundle\Entity\Devis
     */
    public function getDevis() {
        return $this->devis;
    }

    public function __toString() {
        return "Ligne i de devis ";
    }


}

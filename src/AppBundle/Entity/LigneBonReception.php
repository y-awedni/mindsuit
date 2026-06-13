<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LigneBonReception
 *
 * @ORM\Table(name="ligne_bon_reception", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="article_id", columns={"article_id"}), @ORM\Index(name="tva_id", columns={"tva_id"}), @ORM\Index(name="bon_reception_id", columns={"bon_reception_id"})})
 * @ORM\Entity
 */
class LigneBonReception {

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
     * @Assert\NotBlank()
     */
    private $designation;

    /**
     * @var string
     *
     * @ORM\Column(name="qte", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
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
     * @ORM\Column(name="remise", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     * )
     */
    private $remise;

    /**
     * @var string
     *
     * @ORM\Column(name="ttc", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $ttc;

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
     * @Assert\NotBlank()
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
     * @var \BonReception
     *
     * @ORM\ManyToOne(targetEntity="BonReception",inversedBy="ligneBonReceptions",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bon_reception_id", referencedColumnName="id")
     * })
     */
    private $bonReception;

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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * @return LigneBonReception
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
     * Set bonReception
     *
     * @param \AppBundle\Entity\BonReception $bonReception
     *
     * @return LigneBonReception
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

}

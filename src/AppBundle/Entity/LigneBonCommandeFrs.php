<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * LigneBonCommandeFrs
 *
 * @ORM\Table(name="ligne_bon_commande_frs", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="article_id", columns={"article_id"}), @ORM\Index(name="bon_commande_frs_id", columns={"bon_commande_frs_id"})})
 * @ORM\Entity
 */
class LigneBonCommandeFrs {

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
     * @var \BonCommandeFrs
     *
     * @ORM\ManyToOne(targetEntity="BonCommandeFrs",inversedBy="ligneBonCommandeFrss",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bon_commande_frs_id", referencedColumnName="id")
     * })
     */
    private $bonCommandeFrs;

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
     * @return LigneBonCommandeFrs
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
     * @return LigneBonCommandeFrs
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return LigneBonCommandeFrs
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
     * @return LigneBonCommandeFrs
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
     * @return LigneBonCommandeFrs
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
     * Set updatedUser
     *
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return LigneBonCommandeFrs
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
     * @return LigneBonCommandeFrs
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
     * Set bonCommandeFrs
     *
     * @param \AppBundle\Entity\BonCommandeFrs $bonCommandeFrs
     *
     * @return LigneBonCommandeFrs
     */
    public function setBonCommandeFrs(\AppBundle\Entity\BonCommandeFrs $bonCommandeFrs = null) {
        $this->bonCommandeFrs = $bonCommandeFrs;

        return $this;
    }

    /**
     * Get bonCommandeFrs
     *
     * @return \AppBundle\Entity\BonCommandeFrs
     */
    public function getBonCommandeFrs() {
        return $this->bonCommandeFrs;
    }

}

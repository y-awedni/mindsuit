<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Article
 *
 * @ORM\Table(name="article", indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="tva_id", columns={"tva_id"}), @ORM\Index(name="unite_id", columns={"unite_id"}), @ORM\Index(name="categorie_id", columns={"categorie_id"}), @ORM\Index(name="famille_id", columns={"famille_id"}), @ORM\Index(name="sousfamille_id", columns={"sousfamille_id"}), @ORM\Index(name="media_id", columns={"media_id"}), @ORM\Index(name="fournisseur_id", columns={"fournisseur_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ArticleRepository")
 * @UniqueEntity("code")
 * @ORM\HasLifecycleCallbacks()
 */
class Article {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"article"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 12
     * )
     * @Groups({"article"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="designation", type="string", length=255, nullable=true)
     * @Groups({"article"})
     */
    private $designation;

    

    /**
     * @var string
     *
     * @ORM\Column(name="prix_achat", type="decimal", precision=10, scale=3, nullable=true)
     * @Assert\Range(
     *      min = 0
     * )
     * @Groups({"article"})
     */
    private $prixAchat;

    /**
     * @var string
     *
     * @ORM\Column(name="marge", type="decimal", precision=10, scale=3, nullable=true)
     * @Assert\Range(
     *      min = 0
     * )
     * @Groups({"article"})
     */
    private $marge;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_vente_ht", type="decimal", precision=10, scale=3, nullable=true)
     * @Assert\GreaterThan(0)
     * @Groups({"article"})
     */
    private $prixVenteHt;

    /**
     * @var string
     *
     * @ORM\Column(name="prix_vente_ttc", type="decimal", precision=10, scale=3, nullable=true)
     * @Assert\GreaterThan(0)
     * @Groups({"article"})
     */
    private $prixVenteTtc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stockable", type="boolean", nullable=false)
     * @Groups({"article"})
     */
    private $stockable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="service", type="boolean", nullable=false)
     * @Groups({"article"})
     */
    private $service = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="qte_en_depart", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     * @Groups({"article"})
     */
    private $qteEnDepart;

    /**
     * @var integer
     *
     * @ORM\Column(name="qte_en_stock", type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     * @Groups({"article"})
     */
    private $qteEnStock;

    /**
     * @var integer
     *
     * @ORM\Column(name="seuil_alert", type="integer", nullable=true)
     * @Assert\GreaterThan(0)
     * @Groups({"article"})
     */
    private $seuilAlert;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_ajout", type="date", nullable=true)
     * @Groups({"article"})
     */
    private $dateAjout;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     * @Groups({"article"})
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="desactiver_photo", type="boolean", nullable=true)
     * @Groups({"article"})
     */
    private $desactiverPhoto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Groups({"article"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Groups({"article"})
     */
    private $updatedAt;

    /**
     * @var \Tva
     *
     * @ORM\ManyToOne(targetEntity="Tva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tva_id", referencedColumnName="id")
     * })
     * @Groups({"article"})
     */
    private $tva;

    /**
     * @var \Famille
     *
     * @ORM\ManyToOne(targetEntity="Famille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="famille_id", referencedColumnName="id")
     * })
     * @Groups({"famille"})
     */
    private $famille;

    /**
     * @var \Unite
     *
     * @ORM\ManyToOne(targetEntity="Unite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unite_id", referencedColumnName="id")
     * })
     * @Groups({"unite"})
     */
    private $unite;

    /**
     * @var \Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     * @Groups({"categorie"})
     */
    private $categorie;

    /**
     * @var \Sousfamille
     *
     * @ORM\ManyToOne(targetEntity="Sousfamille")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sousfamille_id", referencedColumnName="id")
     * })
     * @Groups({"sousfamille"})
     */
    private $sousfamille;

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
     * @var \Media
     *
     * @ORM\ManyToOne(targetEntity="Media",cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * })
     * @Groups({"media"})
     */
    private $media;

    /**
     * @var \Fournisseur
     *
     * @ORM\ManyToOne(targetEntity="Fournisseur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fournisseur_id", referencedColumnName="id")
     * })
     * @Groups({"fournisseur"})
     */
    private $fournisseur;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Article
     */
    public function setCode($code) {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode() {
        return $this->code;
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
     * Set prixAchat
     *
     * @param string $prixAchat
     *
     * @return Article
     */
    public function setPrixAchat($prixAchat) {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return string
     */
    public function getPrixAchat() {
        return $this->prixAchat;
    }

    /**
     * Set marge
     *
     * @param string $marge
     *
     * @return Article
     */
    public function setMarge($marge) {
        $this->marge = $marge;

        return $this;
    }

    /**
     * Get marge
     *
     * @return string
     */
    public function getMarge() {
        return $this->marge;
    }

    /**
     * Set prixVenteHt
     *
     * @param string $prixVenteHt
     *
     * @return Article
     */
    public function setPrixVenteHt($prixVenteHt) {
        $this->prixVenteHt = $prixVenteHt;

        return $this;
    }

    /**
     * Get prixVenteHt
     *
     * @return string
     */
    public function getPrixVenteHt() {
        return $this->prixVenteHt;
    }

    /**
     * Set prixVenteTtc
     *
     * @param string $prixVenteTtc
     *
     * @return Article
     */
    public function setPrixVenteTtc($prixVenteTtc) {
        $this->prixVenteTtc = $prixVenteTtc;

        return $this;
    }

    /**
     * Get prixVenteTtc
     *
     * @return string
     */
    public function getPrixVenteTtc() {
        return $this->prixVenteTtc;
    }

    /**
     * Set stockable
     *
     * @param boolean $stockable
     *
     * @return Article
     */
    public function setStockable($stockable) {
        $this->stockable = $stockable;

        return $this;
    }

    /**
     * Get stockable
     *
     * @return boolean
     */
    public function getStockable() {
        return $this->stockable;
    }

    /**
     * Set service
     *
     * @param boolean $service
     *
     * @return Article
     */
    public function setService($service) {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return boolean
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Set qteEnDepart
     *
     * @param integer $qteEnDepart
     *
     * @return Article
     */
    public function setQteEnDepart($qteEnDepart) {
        $this->qteEnDepart = $qteEnDepart;

        return $this;
    }

    /**
     * Get qteEnDepart
     *
     * @return integer
     */
    public function getQteEnDepart() {
        return $this->qteEnDepart;
    }

    /**
     * Set qteEnStock
     *
     * @param integer $qteEnStock
     *
     * @return Article
     */
    public function setQteEnStock($qteEnStock) {
        $this->qteEnStock = $qteEnStock;

        return $this;
    }

    /**
     * Get qteEnStock
     *
     * @return integer
     */
    public function getQteEnStock() {
        return $this->qteEnStock;
    }

    /**
     * Set seuilAlert
     *
     * @param integer $seuilAlert
     *
     * @return Article
     */
    public function setSeuilAlert($seuilAlert) {
        $this->seuilAlert = $seuilAlert;

        return $this;
    }

    /**
     * Get seuilAlert
     *
     * @return integer
     */
    public function getSeuilAlert() {
        return $this->seuilAlert;
    }

    /**
     * Set dateAjout
     *
     * @param \DateTime $dateAjout
     *
     * @return Article
     */
    public function setDateAjout($dateAjout) {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    /**
     * Get dateAjout
     *
     * @return \DateTime
     */
    public function getDateAjout() {
        return $this->dateAjout;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Article
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
     * Set desactiverPhoto
     *
     * @param boolean $desactiverPhoto
     *
     * @return Article
     */
    public function setDesactiverPhoto($desactiverPhoto) {
        $this->desactiverPhoto = $desactiverPhoto;

        return $this;
    }

    /**
     * Get desactiverPhoto
     *
     * @return boolean
     */
    public function getDesactiverPhoto() {
        return $this->desactiverPhoto;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Article
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
     * @return Article
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
     * Set tva
     *
     * @param \AppBundle\Entity\Tva $tva
     *
     * @return Article
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
     * Set famille
     *
     * @param \AppBundle\Entity\Famille $famille
     *
     * @return Article
     */
    public function setFamille(\AppBundle\Entity\Famille $famille = null) {
        $this->famille = $famille;

        return $this;
    }

    /**
     * Get famille
     *
     * @return \AppBundle\Entity\Famille
     */
    public function getFamille() {
        return $this->famille;
    }

    /**
     * Set unite
     *
     * @param \AppBundle\Entity\Unite $unite
     *
     * @return Article
     */
    public function setUnite(\AppBundle\Entity\Unite $unite = null) {
        $this->unite = $unite;

        return $this;
    }

    /**
     * Get unite
     *
     * @return \AppBundle\Entity\Unite
     */
    public function getUnite() {
        return $this->unite;
    }

    /**
     * Set categorie
     *
     * @param \AppBundle\Entity\Categorie $categorie
     *
     * @return Article
     */
    public function setCategorie(\AppBundle\Entity\Categorie $categorie = null) {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \AppBundle\Entity\Categorie
     */
    public function getCategorie() {
        return $this->categorie;
    }

    /**
     * Set sousfamille
     *
     * @param \AppBundle\Entity\Sousfamille $sousfamille
     *
     * @return Article
     */
    public function setSousfamille(\AppBundle\Entity\Sousfamille $sousfamille = null) {
        $this->sousfamille = $sousfamille;

        return $this;
    }

    /**
     * Get sousfamille
     *
     * @return \AppBundle\Entity\Sousfamille
     */
    public function getSousfamille() {
        return $this->sousfamille;
    }

    /**
     * Set updatedUser
     *
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return Article
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
     * @return Article
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
     * Set media
     *
     * @param \AppBundle\Entity\Media $media
     *
     * @return Article
     */
    public function setMedia(\AppBundle\Entity\Media $media = null) {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \AppBundle\Entity\Media
     */
    public function getMedia() {
        return $this->media;
    }

    /**
     * Set fournisseur
     *
     * @param \AppBundle\Entity\Fournisseur $fournisseur
     *
     * @return Article
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
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setArticleEnPrePersist() {
        if ($this->prixVenteHt) {
            $tva = 0;
            if ($this->tva) {
                $tva = $this->tva->getTaux();
            }
            $this->prixVenteTtc = $this->prixVenteHt + $this->prixVenteHt / 100 * $tva;
        }
    }
    
    public function __toString() {
        return "".$this->code;
    }

}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Societe
 *
 * @ORM\Table(name="societe", indexes={@ORM\Index(name="user_created_id", columns={"created_user_id"}), @ORM\Index(name="user_updated_id", columns={"updated_user_id"})})
 * @ORM\Entity
 */
class Societe {

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
     * @ORM\Column(name="rs", type="string", length=255, nullable=false)
     */
    private $rs;

    /**
     * @var string
     *
     * @ORM\Column(name="mf", type="string", length=255, nullable=false)
     */
    private $mf;

    /**
     * @var string
     *
     * @ORM\Column(name="rcs", type="string", length=255, nullable=false)
     */
    private $rcs;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=false)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=false)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=false)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postale", type="string", length=255, nullable=false)
     */
    private $codePostale;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=false)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=false)
     */
    private $fax;
    
    

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=false)
     */
    private $mobile;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="desactiver_photo", type="boolean", length=65535, nullable=true)
     */
    private $desactiverPhoto = false;

    /**
     * @var \Media
     *
     * @ORM\OneToOne(targetEntity="Media",cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * })
     */
    private $media;
    
    /**
     * @var \Compte
     *
     * @ORM\OneToOne(targetEntity="Compte",cascade={"all"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="compte_id", referencedColumnName="id")
     * })
     */
    private $compte;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="site_web", type="string", length=255, nullable=true)
     */
    private $siteWeb;

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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_user_id", referencedColumnName="id")
     * })
     */
    private $createdUser;

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
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set rs
     *
     * @param string $rs
     *
     * @return Societe
     */
    public function setRs($rs) {
        $this->rs = $rs;

        return $this;
    }

    /**
     * Get rs
     *
     * @return string
     */
    public function getRs() {
        return $this->rs;
    }

    /**
     * Set mf
     *
     * @param string $mf
     *
     * @return Societe
     */
    public function setMf($mf) {
        $this->mf = $mf;

        return $this;
    }

    /**
     * Get mf
     *
     * @return string
     */
    public function getMf() {
        return $this->mf;
    }

    /**
     * Set rcs
     *
     * @param string $rcs
     *
     * @return Societe
     */
    public function setRcs($rcs) {
        $this->rcs = $rcs;

        return $this;
    }

    /**
     * Get rcs
     *
     * @return string
     */
    public function getRcs() {
        return $this->rcs;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return Societe
     */
    public function setAdresse($adresse) {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse() {
        return $this->adresse;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Societe
     */
    public function setVille($ville) {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille() {
        return $this->ville;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Societe
     */
    public function setPays($pays) {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays() {
        return $this->pays;
    }

    /**
     * Set codePostale
     *
     * @param string $codePostale
     *
     * @return Societe
     */
    public function setCodePostale($codePostale) {
        $this->codePostale = $codePostale;

        return $this;
    }

    /**
     * Get codePostale
     *
     * @return string
     */
    public function getCodePostale() {
        return $this->codePostale;
    }

    /**
     * Set tel
     *
     * @param string $tel
     *
     * @return Societe
     */
    public function setTel($tel) {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return Societe
     */
    public function setFax($fax) {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax() {
        return $this->fax;
    }
    
    

    /**
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Societe
     */
    public function setMobile($mobile) {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string
     */
    public function getMobile() {
        return $this->mobile;
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
     * Set compte
     *
     * @param \AppBundle\Entity\Compte $compte
     *
     * @return Article
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
     * Set email
     *
     * @param string $email
     *
     * @return Client
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     *
     * @return Client
     */
    public function setSiteWeb($siteWeb) {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string
     */
    public function getSiteWeb() {
        return $this->siteWeb;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Societe
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
     * @return Societe
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
     * Set createdUser
     *
     * @param \AppBundle\Entity\User $createdUser
     *
     * @return Societe
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
     * Set updatedUser
     *
     * @param \AppBundle\Entity\User $updatedUser
     *
     * @return Societe
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

    public function getCheminPhoto() {
        return $this->photo;
    }
    
    /**
     * Set desactiverPhoto
     *
     * @param string $desactiverPhoto
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
     * @return string
     */
    public function getDesactiverPhoto() {
        return $this->desactiverPhoto;
    }

}

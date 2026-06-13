<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Fournisseur
 *
 * @ORM\Table(name="fournisseur", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"})})
 * @ORM\Entity
 * @UniqueEntity("code")
 */
class Fournisseur {

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
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="rs", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $rs;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="mf", type="string", length=255, nullable=true)
     */
    private $mf;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rc", type="string", length=255, nullable=true)
     */
    private $rc;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse1", type="string", length=255, nullable=true)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse2", type="string", length=255, nullable=true)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse3", type="string", length=255, nullable=true)
     */
    private $adresse3;

    /**
     * @var string
     *
     * @ORM\Column(name="code_postal", type="string", length=8, nullable=true)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    private $tel;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tel2", type="string", length=255, nullable=true)
     */
    private $tel2;

    /**
     * @var string
     *
     * @ORM\Column(name="mobile", type="string", length=255, nullable=true)
     */
    private $mobile;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="siteweb", type="string", length=255, nullable=true)
     */
    private $siteweb;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

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
     * @return Fournisseur
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
     * Set rs
     *
     * @param string $rs
     *
     * @return Fournisseur
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
     * Set rc
     *
     * @param string $rc
     *
     * @return Societe
     */
    public function setRc($rc) {
        $this->rc = $rc;

        return $this;
    }

    /**
     * Get rc
     *
     * @return string
     */
    public function getRc() {
        return $this->rc;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return Fournisseur
     */
    public function setAdresse1($adresse1) {
        $this->adresse1 = $adresse1;

        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1() {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     *
     * @return Fournisseur
     */
    public function setAdresse2($adresse2) {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2() {
        return $this->adresse2;
    }

    /**
     * Set adresse3
     *
     * @param string $adresse3
     *
     * @return Fournisseur
     */
    public function setAdresse3($adresse3) {
        $this->adresse3 = $adresse3;

        return $this;
    }

    /**
     * Get adresse3
     *
     * @return string
     */
    public function getAdresse3() {
        return $this->adresse3;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Fournisseur
     */
    public function setCodePostal($codePostal) {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal() {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     *
     * @return Fournisseur
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
     * @return Fournisseur
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
     * Set tel
     *
     * @param string $tel
     *
     * @return Fournisseur
     */
    public function setTel($tel) {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel2
     *
     * @return string
     */
    public function getTel2() {
        return $this->tel2;
    }
    
    /**
     * Set tel2
     *
     * @param string $tel2
     *
     * @return Fournisseur
     */
    public function setTel2($tel2) {
        $this->tel2 = $tel2;

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
     * Set mobile
     *
     * @param string $mobile
     *
     * @return Fournisseur
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
     * Set fax
     *
     * @param string $fax
     *
     * @return Fournisseur
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
     * Set email
     *
     * @param string $email
     *
     * @return Fournisseur
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
     * Set siteweb
     *
     * @param string $siteweb
     *
     * @return Fournisseur
     */
    public function setSiteweb($siteweb) {
        $this->siteweb = $siteweb;

        return $this;
    }

    /**
     * Get siteweb
     *
     * @return string
     */
    public function getSiteweb() {
        return $this->siteweb;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Fournisseur
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
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return Article
     */
    public function setUpdatedUser(\App\Entity\User $updatedUser = null) {
        $this->updatedUser = $updatedUser;

        return $this;
    }

    /**
     * Get updatedUser
     *
     * @return \App\Entity\User
     */
    public function getUpdatedUser() {
        return $this->updatedUser;
    }

    /**
     * Set createdUser
     *
     * @param \App\Entity\User $createdUser
     *
     * @return Article
     */
    public function setCreatedUser(\App\Entity\User $createdUser = null) {
        $this->createdUser = $createdUser;

        return $this;
    }

    /**
     * Get createdUser
     *
     * @return \App\Entity\User
     */
    public function getCreatedUser() {
        return $this->createdUser;
    }

    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Fournisseur
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
     * @return Fournisseur
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
     * Get fournisseur
     *
     * @return string
     */
    public function __toString() {
        try {
            return (string) "Code : " . $this->code . " : Rs: " . $this->rs;
        } catch (Exception $e) {
            return ' ';
        }
    }

}

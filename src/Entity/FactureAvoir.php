<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneFactureAvoir;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * FactureAvoir
 *
 * @ORM\Table(name="facture_avoir", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="facture_id", columns={"facture_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\FactureAvoirRepository")
 * @UniqueEntity("code")
 */
class FactureAvoir {

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
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="ht", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $ht;

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $remise;

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $tva;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=3, nullable=false)
     */
    private $total;

    /**
     * Droit de timbre applied to this credit note, captured at creation.
     * Historical avoirs predate timbre on avoirs, so they backfill to 0.000.
     *
     * @var string
     *
     * @ORM\Column(name="timbre", type="decimal", precision=10, scale=3, nullable=false, options={"default": "0.000"})
     */
    private $timbre = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="benifice", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $benifice;

    /**
     * @var string
     *
     * @ORM\Column(name="regle", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $regle;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="termine", type="boolean", nullable=false)
     */
    private $termine = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="from_bl", type="boolean", nullable=true)
     */
    private $fromBl = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     * @Assert\NotBlank()
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_echeance", type="date", nullable=true)
     */
    private $dateEcheance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * @var \Facture
     *
     * @ORM\ManyToOne(targetEntity="Facture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="facture_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $facture;

    /**
     * @ORM\OneToMany(targetEntity="LigneFactureAvoir", mappedBy="factureAvoir",cascade={"all"})
     * @Assert\Valid()
     */
    protected $ligneFactureAvoirs;

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
     * @return FactureAvoir
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
     * Set ht
     *
     * @param string $ht
     *
     * @return FactureAvoir
     */
    public function setHt($ht) {
        $this->ht = $ht;

        return $this;
    }

    /**
     * Get ht
     *
     * @return string
     */
    public function getHt() {
        return $this->ht;
    }

    /**
     * Set remise
     *
     * @param string $remise
     *
     * @return FactureAvoir
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
     * Set tva
     *
     * @param string $tva
     *
     * @return FactureAvoir
     */
    public function setTva($tva) {
        $this->tva = $tva;

        return $this;
    }

    /**
     * Get tva
     *
     * @return string
     */
    public function getTva() {
        return $this->tva;
    }

    /**
     * Set total
     *
     * @param string $total
     *
     * @return FactureAvoir
     */
    public function setTotal($total) {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Set timbre
     *
     * @param string $timbre
     *
     * @return FactureAvoir
     */
    public function setTimbre($timbre) {
        $this->timbre = $timbre;

        return $this;
    }

    /**
     * Get timbre
     *
     * @return string
     */
    public function getTimbre() {
        return $this->timbre;
    }

    /**
     * Set benifice
     *
     * @param string $benifice
     *
     * @return FactureAvoir
     */
    public function setBenifice($benifice) {
        $this->benifice = $benifice;

        return $this;
    }

    /**
     * Get benifice
     *
     * @return string
     */
    public function getBenifice() {
        return $this->benifice;
    }

    /**
     * Set regle
     *
     * @param string $regle
     *
     * @return FactureAvoir
     */
    public function setRegle($regle) {
        $this->regle = $regle;

        return $this;
    }

    /**
     * Get regle
     *
     * @return string
     */
    public function getRegle() {
        return $this->regle;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return FactureAvoir
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
     * Set termine
     *
     * @param boolean $termine
     *
     * @return FactureAvoir
     */
    public function setTermine($termine) {
        $this->termine = $termine;

        return $this;
    }

    /**
     * Get termine
     *
     * @return boolean
     */
    public function getTermine() {
        return $this->termine;
    }

    /**
     * Set fromBl
     *
     * @param boolean $fromBl
     *
     * @return FactureAvoir
     */
    public function setFromBl($fromBl) {
        $this->fromBl = $fromBl;

        return $this;
    }

    /**
     * Get fromBl
     *
     * @return boolean
     */
    public function getFromBl() {
        return $this->fromBl;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return FactureAvoir
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
     * Set dateEcheance
     *
     * @param \DateTime $dateEcheance
     *
     * @return FactureAvoir
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return FactureAvoir
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
     * @return FactureAvoir
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
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return FactureAvoir
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
     * @return FactureAvoir
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
     * Set facture
     *
     * @param \App\Entity\Facture $facture
     *
     * @return FactureAvoir
     */
    public function setFacture(\App\Entity\Facture $facture = null) {
        $this->facture = $facture;

        return $this;
    }

    /**
     * Get facture
     *
     * @return \App\Entity\Facture
     */
    public function getFacture() {
        return $this->facture;
    }

    public function __toString() {
        return "" . $this->code;
    }

    public function __construct() {
        $this->ligneFactureAvoirs = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    /**
     * Get lignesFactures
     *
     * @return \App\Entity\LigneFactureAvoir
     */
    public function getLigneFactureAvoirs() {
        return $this->ligneFactureAvoirs;
    }

    public function addLigneFactureAvoir(LigneFactureAvoir $l) {
        $l->setFactureAvoir($this);
        $this->ligneFactureAvoirs->add($l);
    }

    public function removeLigneFactureAvoir(LigneFactureAvoir $l) {
        $this->ligneFactureAvoirs->removeElement($l);
    }
    
    public function qteOfArticle($ligne){
        
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context) {
        $articles = array();
        $verifArticle = true;
        $messageErreurArticle = "Les articles de cette facture sont : ";
        //tableau des article de cette facture
        foreach ($this->facture->getLignesFactures() as $ligne) {
            if (!in_array($ligne->getArticle()->getCode(), $articles)) {
                $messageErreurArticle .= $ligne->getArticle()->getCode() . "\n";
            }
            array_push($articles, $ligne->getArticle()->getCode());
        }

        //si un article a n'a pas dans cette facture en fait une alerte  sinon en teste la quantite
        foreach ($this->ligneFactureAvoirs as $ligne) {
            if (!in_array($ligne->getArticle()->getCode(), $articles)) {
                $verifArticle = false; //en fait une alerte
            }
        }


        if (!$verifArticle) {
            $context->buildViolation('%messageErreur%')
                    ->setParameter('%messageErreur%', $messageErreurArticle)
                    ->atPath('qte')
                    ->addViolation();
        }
    }

}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\LigneFacture;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Facture
 *
 * @ORM\Table(name="facture", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})}, indexes={@ORM\Index(name="updated_user_id", columns={"updated_user_id"}), @ORM\Index(name="created_user_id", columns={"created_user_id"}), @ORM\Index(name="client_id", columns={"client_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\FactureRepository")
 * @UniqueEntity("code")
 * @ORM\HasLifecycleCallbacks()
 */
class Facture {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Groups({"facture", "lignes"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="ht", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $ht = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="remise", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $remise = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="tva", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $tva = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $total = 0;

    /**
     * Droit de timbre applied to this invoice, captured at creation so the
     * stored total stays self-consistent even if the configured value changes.
     *
     * @var string
     *
     * @ORM\Column(name="timbre", type="decimal", precision=10, scale=3, nullable=false, options={"default": "0.600"})
     * @Groups({"facture", "lignes"})
     */
    private $timbre = '0.600';


    /**
     * @var string
     *
     * @ORM\Column(name="taux_retenu", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $tauxRetenu = 0;
    
    /**
     * @var string
     *
     * @ORM\Column(name="total_retenu", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $totalRetenu = 0;
    
    /**
     * @var string
     *
     * @ORM\Column(name="total_avoir_rembourse", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $totalAvoirRembourse = 0;
    
    /**
     * @var string
     *
     * @ORM\Column(name="total_avoir_non_rembourse", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $totalAvoirNonRembourse = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="benifice", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $benifice;

    /**
     * @var string
     *
     * @ORM\Column(name="regle", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $regle = '0.000';

    /**
     * @var string
     *
     * @ORM\Column(name="reste", type="decimal", precision=10, scale=3, nullable=false)
     * @Groups({"facture", "lignes"})
     */
    private $reste;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     * @Groups({"facture", "lignes"})
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="termine", type="boolean", length=65535, nullable=true)
     * @Groups({"facture", "lignes"})
     */
    private $termine;

    /**
     * @var boolean
     *
     * @ORM\Column(name="from_bl", type="boolean", length=65535, nullable=true)
     * @Groups({"facture", "lignes"})
     */
    private $fromBl;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date_creation", type="date", nullable=true)
     * @Groups({"facture", "lignes"})
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $dateCreation;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date_echeance", type="date", nullable=true)
     * @Groups({"facture", "lignes"})
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $dateEcheance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Groups({"facture", "lignes"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Groups({"facture", "lignes"})
     */
    private $updatedAt;

    /**
     * @var \Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @Groups({"facture", "lignes"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $client;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @Groups({"facture", "lignes"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="updated_user_id", referencedColumnName="id")
     * })
     */
    private $updatedUser;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @Groups({"facture", "lignes"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_user_id", referencedColumnName="id")
     * })
     */
    private $createdUser;

    /**
     * @ORM\OneToMany(targetEntity="LigneFacture", mappedBy="facture",cascade={"all"})
     * @Groups({ "facture"})
     * @Assert\Valid()
     */
    protected $lignesFactures;

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
     * @return Facture
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
     * @return Devis
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
     * @return Devis
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
     * @return Devis
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
     * @return Facture
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
     * @return Facture
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
     * Set tauxRetenu
     *
     * @param string $tauxRetenu
     *
     * @return Facture
     */
    public function setTauxRetenu($tauxRetenu) {
        $this->tauxRetenu = $tauxRetenu;

        return $this;
    }

    /**
     * Get tauxRetenu
     *
     * @return string
     */
    public function getTauxRetenu() {
        return $this->tauxRetenu;
    }
    
    /**
     * Set totalRetenu
     *
     * @param string $totalRetenu
     *
     * @return Facture
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
     * Set totalAvoirRembourse
     *
     * @param string $totalAvoirRembourse
     *
     * @return Facture
     */
    public function setTotalAvoirRembourse($totalAvoirRembourse) {
        $this->totalAvoirRembourse = $totalAvoirRembourse;

        return $this;
    }

    /**
     * Get totalAvoirRembourse
     *
     * @return string
     */
    public function getTotalAvoirRembourse() {
        return $this->totalAvoirRembourse;
    }
    
    /**
     * Set totalAvoirNonRembourse
     *
     * @param string $totalAvoirNonRembourse
     *
     * @return Facture
     */
    public function setTotalAvoirNonRembourse($totalAvoirNonRembourse) {
        $this->totalAvoirNonRembourse = $totalAvoirNonRembourse;

        return $this;
    }

    /**
     * Get totalAvoirNonRembourse
     *
     * @return string
     */
    public function getTotalAvoirNonRembourse() {
        return $this->totalAvoirNonRembourse;
    }
    
    /**
     * Set benifice
     *
     * @param string $benifice
     *
     * @return Facture
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
     * @return Facture
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
     * Set reste
     *
     * @param string $reste
     *
     * @return Facture
     */
    public function setReste($reste) {
        $this->reste = $reste;

        return $this;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Facture
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
     * @return Devis
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
     * @return Devis
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
     * @param string $dateCreation
     *
     * @return Devis
     */
    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return string
     */
    public function getDateCreation() {
        return $this->dateCreation;
    }

    /**
     * Set dateEcheance
     *
     * @param string $dateEcheance
     *
     * @return Devis
     */
    public function setDateEcheance($dateEcheance) {
        $this->dateEcheance = $dateEcheance;

        return $this;
    }

    /**
     * Get dateEcheance
     *
     * @return string
     */
    public function getDateEcheance() {
        return $this->dateEcheance;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Facture
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
     * @return Facture
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
     * @param \App\Entity\Client $client
     *
     * @return Facture
     */
    public function setClient(\App\Entity\Client $client = null) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \App\Entity\Client
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set updatedUser
     *
     * @param \App\Entity\User $updatedUser
     *
     * @return Facture
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
     * @return Facture
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

    public function __toString() {
        return "" . $this->code;
    }

    public function __construct() {
        $this->lignesFactures = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->dateEcheance = new \DateTime();
    }

    /**
     * Get lignesFactures
     *
     * @return \Doctrine\Common\Collections\Collection|\App\Entity\LigneFacture[]
     */
    public function getLignesFactures() {
        return $this->lignesFactures;
    }

    public function addLignesFacture(LigneFacture $l) {
        $l->setFacture($this);
        $this->lignesFactures->add($l);
    }

    public function removeLignesFacture(LigneFacture $l) {
        $this->lignesFactures->removeElement($l);
    }

    /**
     * Get reste
     *
     * @return string
     */
    public function getReste() {
        return $this->reste;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context) {
        $verif = true;
        $tableVerif = [];
        $article = '';
        $qteEnStock = 0;
        foreach ($this->lignesFactures as $ligneI) {
            if ($ligneI->getArticle()->getStockable()) {
                if ($ligneI->getQte() > $ligneI->getArticle()->getQteEnStock()) {
                    $verif = false;
                    $article = $ligneI->getArticle()->getCode();
                    $qteEnStock = $ligneI->getArticle()->getQteEnStock();
                    break;
                } else {
                    if (!in_array($ligneI->getId(), $tableVerif)) {
                        array_push($tableVerif, $ligneI->getId());
                        $qteEnStock = $ligneI->getArticle()->getQteEnStock();
                        $qte = 0;
                        foreach ($this->lignesFactures as $ligneJ) {
                            if ($ligneI->getArticle()->getId() === $ligneJ->getArticle()->getId()) {
                                $qte += $ligneJ->getQte();
                            }
                        }
                        if ($qteEnStock < $qte) {
                            $article = $ligneI->getArticle()->getCode();
                            $qteEnStock = $ligneI->getArticle()->getQteEnStock();
                            $verif = false;
                            break;
                        }
                    }
                }
            }
        }
        if (!$verif) {
            $context->buildViolation('Qte en stock de %article% est %qteEnStock%')
                    ->setParameter('%article%', $article)
                    ->setParameter('%qteEnStock%', $qteEnStock)
                    ->atPath('lignesFactures')
                    ->addViolation();
        }
    }

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="cin", type="string", length=255, nullable=true)
     */
    private $cin;

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Facture
     */
    public function setNom($nom) {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * Set cin
     *
     * @param string $cin
     *
     * @return Facture
     */
    public function setCin($cin) {
        $this->cin = $cin;

        return $this;
    }

    /**
     * Get cin
     *
     * @return string
     */
    public function getCin() {
        return $this->cin;
    }

    /**
     * @ORM\PrePersist
     */
    public function setBenificeTotalEtEnLignesPersist() {
        $lignes = $this->getLignesFactures();
        $benifice = 0;
        foreach ($lignes as $ligne) {
            $qte = $ligne->getQte();
            $prixAchatHt = $ligne->getArticle()->getPrixAchat();
            $tva = $ligne->getTva()->getTaux();
            $prixAchatTtc = $prixAchatHt + $prixAchatHt / 100 * $tva;
            $_benifice = $ligne->getTtc() - ($prixAchatTtc * $qte);
            $ligne->setBenifice($_benifice);
            $benifice += $_benifice;
        }
        $this->setBenifice($benifice);
    }

    /**
     * @ORM\PreUpdate
     */
    public function setBenificeTotalEtEnLignesUpdate() {
        $lignes = $this->getLignesFactures();
        $benifice = 0;
        foreach ($lignes as $ligne) {
            $qte = $ligne->getQte();
            $prixAchatHt = $ligne->getArticle()->getPrixAchat();
            $tva = $ligne->getTva()->getTaux();
            $prixAchatTtc = $prixAchatHt + $prixAchatHt / 100 * $tva;
            $_benifice = $ligne->getTtc() - ($prixAchatTtc * $qte);
            $ligne->setBenifice($_benifice);
            $benifice += $_benifice;
        }
        $this->setBenifice($benifice);
    }

    /**
     * @ORM\PrePersist
     */
    public function setResteValue() {
        $this->reste = $this->total;
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function setResteValueOnUpdate() {
        $this->reste = ($this->total-$this->getRegle())>=0?($this->total-$this->getRegle()):0;
    }

}

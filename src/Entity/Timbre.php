<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Droit de timbre fiscal (Tunisian stamp duty).
 *
 * Single-row configuration: one current value applied to new invoices. The
 * value can change year to year per finance law; each invoice stores the value
 * that applied at its creation (Facture::$timbre), so this row only drives new
 * documents and never rewrites historical ones.
 *
 * @ORM\Table(name="timbre")
 * @ORM\Entity
 */
class Timbre
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="valeur", type="decimal", precision=10, scale=3, nullable=false)
     * @Assert\NotBlank()
     * @Assert\PositiveOrZero()
     */
    private $valeur = '0.600';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getId()
    {
        return $this->id;
    }

    public function setValeur($valeur)
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getValeur()
    {
        return $this->valeur;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function __toString()
    {
        return (string) $this->valeur;
    }
}

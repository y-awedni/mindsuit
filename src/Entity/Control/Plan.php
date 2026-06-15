<?php

namespace App\Entity\Control;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A subscription plan offered to tenants (Starter, Pro, Entreprise...).
 * Lives in the control database.
 */
#[ORM\Entity]
#[ORM\Table(name: 'plan')]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private string $code;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name;

    /** Monthly price in millimes (or DT) — 0 for free/trial-only plans. */
    #[ORM\Column(type: Types::INTEGER)]
    private int $priceMonthly = 0;

    /** Null = unlimited. */
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $maxUsers = null;

    /** Null = unlimited. */
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $maxDocsPerMonth = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPriceMonthly(): int
    {
        return $this->priceMonthly;
    }

    public function setPriceMonthly(int $priceMonthly): self
    {
        $this->priceMonthly = $priceMonthly;

        return $this;
    }

    public function getMaxUsers(): ?int
    {
        return $this->maxUsers;
    }

    public function setMaxUsers(?int $maxUsers): self
    {
        $this->maxUsers = $maxUsers;

        return $this;
    }

    public function getMaxDocsPerMonth(): ?int
    {
        return $this->maxDocsPerMonth;
    }

    public function setMaxDocsPerMonth(?int $maxDocsPerMonth): self
    {
        $this->maxDocsPerMonth = $maxDocsPerMonth;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

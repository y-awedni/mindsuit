<?php

namespace App\Entity\Control;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A subscription plan offered to tenants (Starter, Pro, Entreprise...).
 * Lives in the control database.
 *
 * Limits are stored as a flexible key => value map ({@see PlanLimit} keys),
 * where the value is an integer cap or null for "unlimited". New limit types
 * can be added without a schema change; values are editable from the operator
 * dashboard.
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

    /** Monthly price in millimes (1 DT = 1000). 0 for a free plan. */
    #[ORM\Column(type: Types::INTEGER)]
    private int $priceMonthly = 0;

    /** Yearly price in millimes; null = no yearly option. */
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $priceYearly = null;

    /** Whether the plan can be subscribed to / shown. */
    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = true;

    /**
     * Limit key => cap (int) or null for unlimited. See {@see PlanLimit}.
     *
     * @var array<string,int|null>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $limits = [];

    #[ORM\Column(type: Types::INTEGER)]
    private int $sortOrder = 0;

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

    public function getPriceYearly(): ?int
    {
        return $this->priceYearly;
    }

    public function setPriceYearly(?int $priceYearly): self
    {
        $this->priceYearly = $priceYearly;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return array<string,int|null>
     */
    public function getLimits(): array
    {
        return $this->limits;
    }

    /**
     * @param array<string,int|null> $limits
     */
    public function setLimits(array $limits): self
    {
        $this->limits = $limits;

        return $this;
    }

    /**
     * The cap for a limit key, or null when unlimited / not configured.
     */
    public function getLimit(string $key): ?int
    {
        return $this->limits[$key] ?? null;
    }

    public function setLimit(string $key, ?int $value): self
    {
        $this->limits[$key] = $value;

        return $this;
    }

    /** A finite cap is configured for this key (i.e. not unlimited). */
    public function hasLimit(string $key): bool
    {
        return isset($this->limits[$key]) && $this->limits[$key] !== null;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}

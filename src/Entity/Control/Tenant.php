<?php

namespace App\Entity\Control;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * A tenant = one customer company with its own isolated database.
 * Lives in the control database; the actual ERP data lives in the database
 * named by {@see $dbName}.
 */
#[ORM\Entity]
#[ORM\Table(name: 'tenant')]
class Tenant
{
    public const STATUS_TRIAL = 'trial';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /** The subdomain, e.g. "acme" for acme.moudir.pro. */
    #[ORM\Column(type: Types::STRING, length: 63, unique: true)]
    private string $subdomain;

    /** Display name of the company. */
    #[ORM\Column(type: Types::STRING, length: 150)]
    private string $companyName;

    /** Physical database name, e.g. "tenant_acme". */
    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private string $dbName;

    #[ORM\Column(type: Types::STRING, length: 20)]
    private string $status = self::STATUS_TRIAL;

    #[ORM\ManyToOne(targetEntity: Plan::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Plan $plan = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubdomain(): string
    {
        return $this->subdomain;
    }

    public function setSubdomain(string $subdomain): self
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }

    public function setDbName(string $dbName): self
    {
        $this->dbName = $dbName;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isActive(): bool
    {
        return \in_array($this->status, [self::STATUS_TRIAL, self::STATUS_ACTIVE], true);
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function __toString(): string
    {
        return $this->companyName;
    }
}

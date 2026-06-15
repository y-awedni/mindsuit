<?php

namespace App\Doctrine;

use App\Entity\Control\Tenant;

/**
 * Request-scoped holder of the currently resolved tenant.
 *
 * Populated by {@see \App\EventListener\TenantResolver} (or by CLI commands)
 * and read by application code that needs to know which tenant is active
 * (e.g. per-tenant upload paths). When no tenant is set, the request targets
 * the control plane (apex domain: marketing, signup, operator back-office).
 */
class TenantContext
{
    private ?Tenant $tenant = null;

    public function setTenant(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function getSubdomain(): ?string
    {
        return $this->tenant?->getSubdomain();
    }
}

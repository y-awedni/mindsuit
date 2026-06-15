<?php

namespace App\Tenant;

/**
 * Thrown when a tenant cannot be provisioned (invalid/unavailable subdomain,
 * bad input, or a failure during creation). Carries a user-safe message so
 * callers (CLI, signup endpoint) can surface it directly.
 */
class ProvisioningException extends \RuntimeException
{
}

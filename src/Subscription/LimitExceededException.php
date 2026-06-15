<?php

namespace App\Subscription;

/**
 * Thrown when an action would exceed the tenant's current plan limit
 * (e.g. monthly documents, active users). Carries a user-safe French message
 * for direct display.
 */
class LimitExceededException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly string $limitKey,
        public readonly int $cap,
        public readonly int $used,
    ) {
        parent::__construct($message);
    }
}

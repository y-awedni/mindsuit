<?php

namespace App\Subscription;

/**
 * Registry of known plan-limit keys.
 *
 * Limit *values* live as data on each {@see \App\Entity\Control\Plan} (a JSON
 * map, editable from the operator dashboard). The *keys* are declared here so
 * enforcement code and the dashboard form know which limits exist. Add a new
 * limit by declaring a constant + a label below and enforcing it where
 * relevant — no schema change required.
 */
final class PlanLimit
{
    /** Max billing documents (factures) creatable per calendar month. */
    public const DOCS_PER_MONTH = 'docsPerMonth';

    /** Max active users in the tenant. */
    public const USERS = 'users';

    /**
     * Human labels for the dashboard, keyed by limit constant.
     *
     * @return array<string,string>
     */
    public static function labels(): array
    {
        return [
            self::DOCS_PER_MONTH => 'Factures par mois',
            self::USERS => 'Utilisateurs',
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::labels());
    }
}

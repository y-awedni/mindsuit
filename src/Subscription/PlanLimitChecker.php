<?php

namespace App\Subscription;

use App\Doctrine\TenantContext;
use App\Entity\Control\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Enforces plan limits for the currently-resolved tenant.
 *
 * Reads the cap from the tenant's plan (control DB) and counts usage in the
 * tenant DB. Limit keys are declared in {@see PlanLimit}; values are pure data
 * editable from the operator dashboard. Operator-configured "unlimited"
 * (null) and missing tenant/plan both fall through to no enforcement.
 */
class PlanLimitChecker
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        #[Autowire(service: 'doctrine.orm.tenant_entity_manager')]
        private readonly EntityManagerInterface $tenantEm,
    ) {
    }

    /**
     * Returns ['cap' => int|null, 'used' => int]. cap=null means unlimited.
     *
     * @return array{cap:int|null,used:int}
     */
    public function usage(string $limitKey): array
    {
        $tenant = $this->tenantContext->getTenant();
        $cap = $tenant?->getPlan()?->getLimit($limitKey);

        return ['cap' => $cap, 'used' => $this->countUsage($limitKey)];
    }

    /**
     * @throws LimitExceededException when adding $increment would exceed the cap.
     */
    public function assertCanAdd(string $limitKey, int $increment = 1): void
    {
        ['cap' => $cap, 'used' => $used] = $this->usage($limitKey);
        if ($cap === null) {
            return;
        }

        if ($used + $increment > $cap) {
            throw new LimitExceededException(
                $this->formatMessage($limitKey, $cap),
                $limitKey,
                $cap,
                $used
            );
        }
    }

    private function countUsage(string $limitKey): int
    {
        if (!$this->tenantContext->hasTenant()) {
            return 0;
        }

        return match ($limitKey) {
            PlanLimit::DOCS_PER_MONTH => $this->countFacturesThisMonth(),
            PlanLimit::USERS => $this->countActiveUsers(),
            default => 0,
        };
    }

    private function countFacturesThisMonth(): int
    {
        $start = new \DateTimeImmutable('first day of this month 00:00:00');
        $end = new \DateTimeImmutable('first day of next month 00:00:00');

        return (int) $this->tenantEm->createQuery(
            'SELECT COUNT(f.id) FROM App\Entity\Facture f WHERE f.dateCreation >= :start AND f.dateCreation < :end'
        )
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->getSingleScalarResult();
    }

    private function countActiveUsers(): int
    {
        return (int) $this->tenantEm->createQuery(
            'SELECT COUNT(u.id) FROM App\Entity\User u WHERE u.enabled = true'
        )->getSingleScalarResult();
    }

    private function formatMessage(string $limitKey, int $cap): string
    {
        return match ($limitKey) {
            PlanLimit::DOCS_PER_MONTH => sprintf(
                'Vous avez atteint la limite de votre plan (%d factures par mois). Passez à un plan supérieur pour continuer.',
                $cap
            ),
            PlanLimit::USERS => sprintf(
                'Vous avez atteint la limite d\'utilisateurs de votre plan (%d). Passez à un plan supérieur pour en ajouter.',
                $cap
            ),
            default => sprintf('Limite « %s » atteinte (%d). Passez à un plan supérieur pour continuer.', $limitKey, $cap),
        };
    }
}

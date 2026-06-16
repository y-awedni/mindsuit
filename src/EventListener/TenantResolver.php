<?php

namespace App\EventListener;

use App\Doctrine\TenantConnection;
use App\Doctrine\TenantContext;
use App\Entity\Control\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resolves the active tenant from the request Host on every (main) request and
 * points the tenant connection at the right database.
 *
 *  - <base domain> / www.<base domain> -> control plane (no tenant): the
 *    marketing site, signup, operator back-office.
 *  - <sub>.<base domain>               -> the tenant with subdomain <sub>.
 *  - If DEFAULT_TENANT is set (dev/test), it is used when no subdomain is
 *    present, so the app works on bare http://localhost.
 *
 * Runs before the security firewall (priority 200) so the User provider's
 * queries hit the resolved tenant database.
 */
class TenantResolver
{
    public function __construct(
        private readonly EntityManagerInterface $controlEm,
        private readonly TenantConnection $tenantConnection,
        private readonly TenantContext $tenantContext,
        private readonly string $baseDomain,
        private readonly string $defaultTenant = '',
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $subdomain = $this->resolveSubdomain($event->getRequest()->getHost());

        if ($subdomain === null) {
            // Control plane: no tenant.
            $this->tenantContext->setTenant(null);

            return;
        }

        $tenant = $this->controlEm->getRepository(Tenant::class)->findOneBy(['subdomain' => $subdomain]);

        if ($tenant === null) {
            throw new NotFoundHttpException(sprintf('Aucun espace trouvé pour « %s ».', $subdomain));
        }

        $this->tenantContext->setTenant($tenant);
        $this->tenantConnection->selectDatabase($tenant->getDbName());

        if (!$tenant->isActive()) {
            $path = $event->getRequest()->getPathInfo();
            if (!str_starts_with($path, '/subscription/expired') && !str_starts_with($path, '/login') && !str_starts_with($path, '/logout')) {
                $event->setResponse(new RedirectResponse('/subscription/expired?reason=suspended'));
            }
        }
    }

    private function resolveSubdomain(string $host): ?string
    {
        $host = strtolower($host);
        $base = strtolower($this->baseDomain);

        $subdomain = null;

        if ($host === $base || $host === 'www.' . $base) {
            $subdomain = null;
        } elseif (str_ends_with($host, '.' . $base)) {
            $subdomain = substr($host, 0, -\strlen('.' . $base));
        }

        if (($subdomain === null || $subdomain === '' || $subdomain === 'www') && $this->defaultTenant !== '') {
            return $this->defaultTenant;
        }

        return ($subdomain === '' || $subdomain === 'www' || $subdomain === 'admin') ? null : $subdomain;
    }
}

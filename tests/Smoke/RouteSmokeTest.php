<?php

namespace Tests\Smoke;

use App\Entity\Control\Tenant;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RouteSmokeTest extends WebTestCase
{
    private const KNOWN_BROKEN = [
        'bonreception_boncommande_new',
        'ligneboncommandefrs_new',
        'lignebonlivraison_index',
        'lignebonlivraison_new',
        'lignedevis_index',
        'lignedevis_new',
        'lignefactureavoir_index',
        'lignefacture_index',
        'lignefacture_new',
        'lignereglement_index',
        'lignereglement_new',
        'articles_stats_id',
        'client_stats_id',
        'fournisseur_stats_id',
    ];

    public function testAllMainRoutesAreNotServerErrors(): void
    {
        $client = static::createClient();
        $client->disableReboot();
        $this->logIn($client);

        $router = static::getContainer()->get('router');
        $failures = [];
        $checked = 0;

        foreach ($router->getRouteCollection() as $name => $route) {
            if (str_starts_with($name, '_')) {
                continue;
            }
            if (\in_array($name, ['app_logout', 'app_login_check'], true)) {
                continue;
            }
            if (\in_array($name, self::KNOWN_BROKEN, true)) {
                continue;
            }
            $path = $route->getPath();
            if (str_contains($path, '{')) {
                continue;
            }
            if (str_contains($name, 'export') || str_contains($path, 'export')) {
                continue;
            }
            $methods = $route->getMethods();
            if ($methods && !\in_array('GET', $methods, true)) {
                continue;
            }

            ++$checked;
            try {
                $client->request('GET', $path);
                $status = $client->getResponse()->getStatusCode();
                if ($status >= 500) {
                    $failures[] = sprintf('%s (%s) -> %d', $name, $path, $status);
                }
            } catch (\Throwable $e) {
                $failures[] = sprintf('%s (%s) -> %s: %s', $name, $path, get_class($e), $e->getMessage());
            }
        }

        $this->assertGreaterThan(20, $checked, 'Expected to smoke-test the main navigation routes.');
        $this->assertSame([], $failures, "Routes returning 5xx:\n" . implode("\n", $failures));
    }

    private function logIn(KernelBrowser $client): void
    {
        $container = static::getContainer();

        // The smoke test runs on bare localhost -> DEFAULT_TENANT (minduos).
        // Mirror TenantResolver here so the pre-request User lookup (below)
        // hits the tenant database rather than the control DB.
        $tenant = $container->get('doctrine')->getManager('default')
            ->getRepository(Tenant::class)->findOneBy(['subdomain' => 'minduos']);
        self::assertNotNull($tenant, 'The "minduos" tenant must exist in the control DB.');
        $container->get('doctrine.dbal.tenant_connection')->selectDatabase($tenant->getDbName());

        $em = $container->get('doctrine')->getManager('tenant');
        $user = $em->getRepository(User::class)->findOneBy(['username' => 'admin']);
        self::assertNotNull($user, 'The admin user must exist in the database.');

        $session = $container->get('session.factory')->createSession();
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }
}

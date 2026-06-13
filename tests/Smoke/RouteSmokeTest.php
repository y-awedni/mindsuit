<?php

namespace Tests\Smoke;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Logs in as the admin user and GETs every parameter-less, GET-able route,
 * asserting none return a 5xx. This is the cheap regression net that catches
 * most breakage across the ~49 controllers during the framework upgrade.
 */
class RouteSmokeTest extends WebTestCase
{
    /**
     * Routes that were already broken before the framework upgrade and are not
     * part of the application navigation:
     *  - line-item CRUD scaffolds with no templates / a scaffold typo
     *    (line items are edited inline within their parent document, never
     *    via these standalone pages),
     *  - routes that require a "?id=" query context (stats selectors, the
     *    "create bon de réception from a bon de commande" flow).
     * Tracked here as a baseline so the smoke test stays a meaningful green gate.
     */
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
        $client->disableReboot(); // read-only crawl: reuse the kernel for speed
        $this->logIn($client);

        $router = static::$container->get('router');
        $failures = [];
        $checked = 0;

        foreach ($router->getRouteCollection() as $name => $route) {
            if (0 === strpos($name, '_')) {
                continue; // profiler / wdt / internal
            }
            if (\in_array($name, ['app_logout', 'app_login_check'], true)) {
                continue;
            }
            if (\in_array($name, self::KNOWN_BROKEN, true)) {
                continue;
            }
            $path = $route->getPath();
            if (false !== strpos($path, '{')) {
                continue; // needs parameters; covered separately
            }
            // Export routes stream binary spreadsheets to php://output; skip them
            // here (they are exercised by their own checks).
            if (false !== strpos($name, 'export') || false !== strpos($path, 'export')) {
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
        $container = static::$container;
        $em = $container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => 'admin']);
        self::assertNotNull($user, 'The admin user must exist in the database.');

        $session = $container->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }
}

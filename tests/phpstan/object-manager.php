<?php

use App\Kernel;

require __DIR__ . '/../../vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(__DIR__ . '/../../.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();

// ERP repositories use the tenant EM; Control entities in Operator controllers
// are in the default EM but have no custom repositories, so they don't trigger
// the phpstan-doctrine entity-lookup path.
return $kernel->getContainer()->get('doctrine')->getManager('tenant');

<?php

use App\Kernel;

require __DIR__ . '/../../vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(__DIR__ . '/../../.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();

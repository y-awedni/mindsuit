<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require __DIR__ . '/../../vendor/autoload.php';
(new Symfony\Component\Dotenv\Dotenv())->bootEnv(__DIR__ . '/../../.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));

return new Application($kernel);

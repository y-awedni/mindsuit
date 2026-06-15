<?php

// Standalone Doctrine Migrations config for the per-tenant ERP schema.
// Run against the tenant entity manager, once per tenant database (the
// tenants:migrate command switches the connection to each tenant DB first):
//
//   php bin/console doctrine:migrations:migrate \
//       --configuration=migrations/tenant.php --em=tenant --no-interaction

return [
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions',
    ],
    'migrations_paths' => [
        'DoctrineMigrations' => __DIR__ . '/tenant',
    ],
];

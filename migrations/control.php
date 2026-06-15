<?php

// Standalone Doctrine Migrations config for the CONTROL plane schema
// (tenants, plans, subscriptions, owners, payments). Run against the control
// entity manager:
//
//   php bin/console doctrine:migrations:migrate \
//       --configuration=migrations/control.php --em=default --no-interaction

return [
    // Distinct table so the control version log never collides with a
    // tenant's doctrine_migration_versions (the tenant connection bootstraps
    // to the control DB before switching).
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions_control',
    ],
    'migrations_paths' => [
        'DoctrineMigrations\\Control' => __DIR__ . '/control',
    ],
];

<?php

namespace App\Doctrine;

/**
 * Runs the per-tenant ERP migrations against a single tenant database.
 *
 * Each run is a fresh subprocess with DATABASE_URL pointed at the target
 * tenant database. A separate process is required because Doctrine's
 * migrations DependencyFactory freezes after first use, so it cannot be
 * reconfigured for a second database within the same process (e.g. when
 * looping over every tenant).
 *
 * Migrations are the single source of truth for the tenant schema: a fresh
 * tenant is built by running the migration chain from empty.
 */
class TenantMigrator
{
    public function __construct(
        private readonly string $projectDir,
        private readonly string $databaseUrl,
    ) {
    }

    /**
     * @return array{0:int,1:string} [exitCode, combinedOutput]
     */
    public function migrate(string $dbName): array
    {
        $url = $this->databaseUrlFor($dbName);

        $cmd = sprintf(
            'cd %s && DATABASE_URL=%s php bin/console doctrine:migrations:migrate '
            . '--configuration=migrations/tenant.php --em=tenant --no-interaction --allow-no-migration 2>&1',
            escapeshellarg($this->projectDir),
            escapeshellarg($url)
        );

        $lines = [];
        $exit = 0;
        exec($cmd, $lines, $exit);

        return [$exit, implode("\n", $lines)];
    }

    private function databaseUrlFor(string $dbName): string
    {
        // Swap the database name in the base DATABASE_URL (…@host:port/<db>?…).
        return preg_replace('#(@[^/]+/)[^?]*#', '${1}' . $dbName, $this->databaseUrl, 1);
    }
}

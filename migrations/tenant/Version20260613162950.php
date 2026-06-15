<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Baseline marker — no-op.
 *
 * The legacy schema was created ad-hoc; this migration anchors DoctrineMigrationsBundle
 * so future `doctrine:migrations:diff` runs only emit deltas from this point.
 * Existing databases mark this version applied via `migrations:version --add --all`;
 * no SQL runs on them.
 *
 * For provisioning brand-new tenant databases (Phase 4), a separate "create from
 * mapping" path will be added rather than reusing this no-op.
 */
final class Version20260613162950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Baseline marker — no-op, anchors future migrations.';
    }

    public function up(Schema $schema): void
    {
    }

    public function down(Schema $schema): void
    {
    }
}

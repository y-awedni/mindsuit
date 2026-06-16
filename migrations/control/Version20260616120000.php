<?php

declare(strict_types=1);

namespace DoctrineMigrations\Control;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Seeds a default operator admin user if the table is empty.
 * Default credentials: admin@moudir.pro / Admin@Moudir2025
 * Change the password after first login.
 */
final class Version20260616120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed default operator admin user if none exists';
    }

    public function up(Schema $schema): void
    {
        $count = (int) $this->connection->fetchOne('SELECT COUNT(*) FROM admin_user');
        if ($count > 0) {
            return;
        }

        $hash = password_hash('Admin@Moudir2025', PASSWORD_BCRYPT, ['cost' => 13]);
        $this->connection->executeStatement(
            "INSERT INTO admin_user (email, password, roles, created_at) VALUES (?, ?, ?, NOW())",
            ['admin@moudir.pro', $hash, json_encode(['ROLE_OPERATOR'])]
        );
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement("DELETE FROM admin_user WHERE email = 'admin@moudir.pro'");
    }
}

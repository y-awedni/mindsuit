<?php

declare(strict_types=1);

namespace DoctrineMigrations\Control;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260615133137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan ADD price_yearly INT DEFAULT NULL, ADD active TINYINT(1) NOT NULL, ADD limits JSON NOT NULL, ADD sort_order INT NOT NULL, DROP max_users, DROP max_docs_per_month');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan ADD max_docs_per_month INT DEFAULT NULL, DROP active, DROP limits, DROP sort_order, CHANGE price_yearly max_users INT DEFAULT NULL');
    }
}

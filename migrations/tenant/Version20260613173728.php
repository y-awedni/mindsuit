<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Make the droit de timbre a configurable, per-document value.
 *
 *  - Adds the `timbre` config table with a single seeded row (0.600).
 *  - Adds `facture.timbre`, backfilling existing rows to 0.600 (every historical
 *    facture had 0.600 baked into its stored total).
 *  - Adds `facture_avoir.timbre`, backfilling existing rows to 0.000 (historical
 *    avoirs predate timbre on credit notes, so their stored total excludes it).
 */
final class Version20260613173728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Configurable per-document droit de timbre (timbre table + facture/facture_avoir columns).';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE timbre (id INT AUTO_INCREMENT NOT NULL, valeur NUMERIC(10, 3) NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql("INSERT INTO timbre (valeur, updated_at) VALUES ('0.600', NOW())");
        $this->addSql("ALTER TABLE facture ADD timbre NUMERIC(10, 3) NOT NULL DEFAULT '0.600'");
        $this->addSql("ALTER TABLE facture_avoir ADD timbre NUMERIC(10, 3) NOT NULL DEFAULT '0.000'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE facture DROP timbre');
        $this->addSql('ALTER TABLE facture_avoir DROP timbre');
        $this->addSql('DROP TABLE timbre');
    }
}

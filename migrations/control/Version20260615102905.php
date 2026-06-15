<?php

declare(strict_types=1);

namespace DoctrineMigrations\Control;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260615102905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE owner (id INT AUTO_INCREMENT NOT NULL, tenant_id INT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_CF60E67CE7927C74 (email), INDEX IDX_CF60E67C9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, subscription_id INT NOT NULL, amount INT NOT NULL, status VARCHAR(20) NOT NULL, provider VARCHAR(40) DEFAULT NULL, provider_ref VARCHAR(191) DEFAULT NULL, paid_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6D28840D9A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, price_monthly INT NOT NULL, max_users INT DEFAULT NULL, max_docs_per_month INT DEFAULT NULL, UNIQUE INDEX UNIQ_DD5A5B7D77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, tenant_id INT NOT NULL, plan_id INT NOT NULL, status VARCHAR(20) NOT NULL, trial_ends_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', current_period_end DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', provider VARCHAR(40) DEFAULT NULL, provider_ref VARCHAR(191) DEFAULT NULL, INDEX IDX_A3C664D39033212A (tenant_id), INDEX IDX_A3C664D3E899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, plan_id INT DEFAULT NULL, subdomain VARCHAR(63) NOT NULL, company_name VARCHAR(150) NOT NULL, db_name VARCHAR(64) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_4E59C462C1D5962E (subdomain), UNIQUE INDEX UNIQ_4E59C462628DE0D9 (db_name), INDEX IDX_4E59C462E899029B (plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE owner ADD CONSTRAINT FK_CF60E67C9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C462E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE owner DROP FOREIGN KEY FK_CF60E67C9033212A');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9A1887DC');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D39033212A');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3E899029B');
        $this->addSql('ALTER TABLE tenant DROP FOREIGN KEY FK_4E59C462E899029B');
        $this->addSql('DROP TABLE owner');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE tenant');
    }
}

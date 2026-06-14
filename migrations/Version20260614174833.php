<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260614174833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP qte, CHANGE service service TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE banque CHANGE created_user_id created_user_id INT DEFAULT NULL, CHANGE updated_user_id updated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bon_commande_frs CHANGE termine termine TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE bon_livraison CHANGE taux_retenu taux_retenu NUMERIC(10, 3) NOT NULL, CHANGE total_retenu total_retenu NUMERIC(10, 3) NOT NULL, CHANGE regle regle NUMERIC(10, 3) NOT NULL, CHANGE reste reste NUMERIC(10, 3) NOT NULL, CHANGE termine termine TINYINT(1) NOT NULL, CHANGE converted converted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE bon_reception CHANGE taux_retenu taux_retenu NUMERIC(10, 3) NOT NULL, CHANGE total_retenu total_retenu NUMERIC(10, 3) NOT NULL, CHANGE regle regle NUMERIC(10, 3) NOT NULL, CHANGE reste reste NUMERIC(10, 3) NOT NULL, CHANGE termine termine TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE bon_reception RENAME INDEX bon_commande_id TO IDX_C77F2B94B4B54061');
        $this->addSql('ALTER TABLE categorie CHANGE updated_user_id updated_user_id INT DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE client CHANGE updated_user_id updated_user_id INT DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL, CHANGE passager passager TINYINT(1) NOT NULL, CHANGE remise remise NUMERIC(10, 0) NOT NULL, CHANGE total_avoir_rembourse total_avoir_rembourse NUMERIC(10, 3) NOT NULL, CHANGE total_avoir_non_rembourse total_avoir_non_rembourse NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE compte CHANGE banque_id banque_id INT DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL, CHANGE updated_user_id updated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE devis CHANGE ht ht NUMERIC(10, 3) NOT NULL, CHANGE remise remise NUMERIC(10, 3) NOT NULL, CHANGE tva tva NUMERIC(10, 3) NOT NULL, CHANGE termine termine TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE facture CHANGE taux_retenu taux_retenu NUMERIC(10, 3) NOT NULL, CHANGE total_retenu total_retenu NUMERIC(10, 3) NOT NULL, CHANGE total_avoir_rembourse total_avoir_rembourse NUMERIC(10, 3) NOT NULL, CHANGE total_avoir_non_rembourse total_avoir_non_rembourse NUMERIC(10, 3) NOT NULL, CHANGE benifice benifice NUMERIC(10, 3) NOT NULL, CHANGE regle regle NUMERIC(10, 3) NOT NULL, CHANGE reste reste NUMERIC(10, 3) NOT NULL, CHANGE termine termine TINYINT(1) DEFAULT NULL, CHANGE from_bl from_bl TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE facture_avoir CHANGE termine termine TINYINT(1) NOT NULL, CHANGE from_bl from_bl TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE famille CHANGE updated_user_id updated_user_id INT DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE famille RENAME INDEX categorie_id TO IDX_2473F213BCF5E72D');
        $this->addSql('ALTER TABLE fournisseur CHANGE updated_user_id updated_user_id INT DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_bon_commande_frs CHANGE bon_commande_frs_id bon_commande_frs_id INT DEFAULT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE ligne_bon_livraison CHANGE bon_livraison_id bon_livraison_id INT DEFAULT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE ligne_bon_reception CHANGE bon_reception_id bon_reception_id INT DEFAULT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE ligne_devis CHANGE devis_id devis_id INT DEFAULT NULL, CHANGE designation designation VARCHAR(255) DEFAULT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL, CHANGE remise remise NUMERIC(10, 0) NOT NULL, CHANGE ttc ttc NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE ligne_devis RENAME INDEX devis_id TO IDX_888B2F1B41DEFADA');
        $this->addSql('ALTER TABLE ligne_facture CHANGE facture_id facture_id INT DEFAULT NULL, CHANGE designation designation VARCHAR(2000) DEFAULT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL, CHANGE remise remise NUMERIC(10, 0) NOT NULL, CHANGE benifice benifice NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE ligne_facture RENAME INDEX facture_id TO IDX_611F5A297F2DEE08');
        $this->addSql('ALTER TABLE ligne_facture_avoir CHANGE facture_avoir_id facture_avoir_id INT DEFAULT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL, CHANGE qte_max qte_max NUMERIC(10, 3) NOT NULL, CHANGE stock stock TINYINT(1) NOT NULL, CHANGE reglement reglement TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ligne_reglement CHANGE date_reglement date_reglement DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_reglement RENAME INDEX mode_reglement_id TO mode_reglement');
        $this->addSql('ALTER TABLE media CHANGE name name VARCHAR(255) NOT NULL, CHANGE path path VARCHAR(255) NOT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE mouvement CHANGE total_retenu total_retenu NUMERIC(10, 3) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX reglement_id ON mouvement (reglement_id)');
        $this->addSql('ALTER TABLE mouvement RENAME INDEX compte_id TO IDX_5B51FC3EF2C56620');
        $this->addSql('ALTER TABLE notification CHANGE updated_user_id updated_user_id INT DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE retenu CHANGE taux taux NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE societe DROP INDEX compte_id, ADD UNIQUE INDEX UNIQ_19653DBDF2C56620 (compte_id)');
        $this->addSql('ALTER TABLE societe DROP INDEX media_id, ADD UNIQUE INDEX UNIQ_19653DBDEA9FDD75 (media_id)');
        $this->addSql('ALTER TABLE societe CHANGE rcs rcs VARCHAR(255) NOT NULL, CHANGE tel tel VARCHAR(255) NOT NULL, CHANGE fax fax VARCHAR(255) NOT NULL, CHANGE mobile mobile VARCHAR(255) NOT NULL, CHANGE desactiver_photo desactiver_photo TINYINT(1) DEFAULT NULL, CHANGE created_user_id created_user_id INT DEFAULT NULL, CHANGE updated_user_id updated_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE societe ADD CONSTRAINT FK_19653DBDE104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE societe ADD CONSTRAINT FK_19653DBDBB649746 FOREIGN KEY (updated_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX famille_id ON sousfamille (famille_id)');
        // The legacy stock table has misnamed/duplicate indexes on fournisseur_id
        // and client_id, two of which back the original stock_ibfk_2/3 foreign
        // keys. Drop those FKs first so the indexes can be normalized, then
        // re-add the FKs once the IDX_ indexes exist. (stock_ibfk_1 on article_id
        // is untouched.)
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY stock_ibfk_2');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY stock_ibfk_3');
        $this->addSql('DROP INDEX fournisseur_id_3 ON stock');
        $this->addSql('DROP INDEX fournisseur_id_2 ON stock');
        $this->addSql('DROP INDEX fournisseur_id ON stock');
        $this->addSql('ALTER TABLE stock CHANGE type_doc type_doc VARCHAR(255) NOT NULL, CHANGE qte qte NUMERIC(10, 3) NOT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656607F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660D8D16068 FOREIGN KEY (bon_livraison_id) REFERENCES bon_livraison (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660D192482A FOREIGN KEY (facture_avoir_id) REFERENCES facture_avoir (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656602A91441F FOREIGN KEY (bon_reception_id) REFERENCES bon_reception (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660BB649746 FOREIGN KEY (updated_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX fournisseur_id ON stock (fournisseur_id)');
        $this->addSql('ALTER TABLE stock RENAME INDEX facture_id TO IDX_4B3656607F2DEE08');
        $this->addSql('ALTER TABLE stock RENAME INDEX bon_livraison_id TO IDX_4B365660D8D16068');
        $this->addSql('ALTER TABLE stock RENAME INDEX facture_avoir_id TO IDX_4B365660D192482A');
        $this->addSql('ALTER TABLE stock RENAME INDEX bon_reception_id TO IDX_4B3656602A91441F');
        $this->addSql('ALTER TABLE stock RENAME INDEX client_id TO IDX_4B36566019EB6921');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B365660670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE tva ADD CONSTRAINT FK_EF699620BB649746 FOREIGN KEY (updated_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tva ADD CONSTRAINT FK_EF699620E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE unite ADD CONSTRAINT FK_1D64C118BB649746 FOREIGN KEY (updated_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE unite ADD CONSTRAINT FK_1D64C118E104C1D3 FOREIGN KEY (created_user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD qte INT DEFAULT NULL, CHANGE service service TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE banque CHANGE created_user_id created_user_id INT NOT NULL, CHANGE updated_user_id updated_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE bon_commande_frs CHANGE termine termine TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE bon_livraison CHANGE taux_retenu taux_retenu NUMERIC(10, 3) DEFAULT NULL, CHANGE total_retenu total_retenu NUMERIC(10, 3) DEFAULT NULL, CHANGE regle regle NUMERIC(10, 3) DEFAULT \'0.000\' NOT NULL, CHANGE reste reste NUMERIC(10, 3) DEFAULT NULL, CHANGE termine termine TINYINT(1) DEFAULT 0 NOT NULL, CHANGE converted converted TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE bon_reception CHANGE taux_retenu taux_retenu NUMERIC(10, 3) DEFAULT NULL, CHANGE total_retenu total_retenu NUMERIC(10, 3) DEFAULT NULL, CHANGE regle regle NUMERIC(10, 3) DEFAULT \'0.000\' NOT NULL, CHANGE reste reste NUMERIC(10, 3) DEFAULT NULL, CHANGE termine termine TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE bon_reception RENAME INDEX idx_c77f2b94b4b54061 TO bon_commande_id');
        $this->addSql('ALTER TABLE categorie CHANGE updated_user_id updated_user_id INT NOT NULL, CHANGE created_user_id created_user_id INT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE client CHANGE updated_user_id updated_user_id INT NOT NULL, CHANGE created_user_id created_user_id INT NOT NULL, CHANGE passager passager TINYINT(1) DEFAULT 0, CHANGE remise remise NUMERIC(10, 3) DEFAULT \'0.000\', CHANGE total_avoir_rembourse total_avoir_rembourse NUMERIC(10, 3) DEFAULT NULL, CHANGE total_avoir_non_rembourse total_avoir_non_rembourse NUMERIC(10, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE compte CHANGE banque_id banque_id INT NOT NULL, CHANGE created_user_id created_user_id INT NOT NULL, CHANGE updated_user_id updated_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE devis CHANGE ht ht NUMERIC(10, 3) DEFAULT \'0.000\' NOT NULL, CHANGE remise remise NUMERIC(10, 3) DEFAULT \'0.000\' NOT NULL, CHANGE tva tva NUMERIC(10, 3) DEFAULT \'0.000\' NOT NULL, CHANGE termine termine TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE facture CHANGE taux_retenu taux_retenu NUMERIC(10, 3) DEFAULT NULL, CHANGE total_retenu total_retenu NUMERIC(10, 3) DEFAULT NULL, CHANGE total_avoir_rembourse total_avoir_rembourse NUMERIC(10, 3) DEFAULT NULL, CHANGE total_avoir_non_rembourse total_avoir_non_rembourse NUMERIC(10, 3) DEFAULT NULL, CHANGE benifice benifice NUMERIC(10, 3) DEFAULT NULL, CHANGE regle regle NUMERIC(10, 3) DEFAULT NULL, CHANGE reste reste NUMERIC(10, 3) DEFAULT NULL, CHANGE termine termine TINYINT(1) DEFAULT 0 NOT NULL, CHANGE from_bl from_bl TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE facture_avoir CHANGE termine termine TINYINT(1) DEFAULT 0 NOT NULL, CHANGE from_bl from_bl TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE famille CHANGE updated_user_id updated_user_id INT NOT NULL, CHANGE created_user_id created_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE famille RENAME INDEX idx_2473f213bcf5e72d TO categorie_id');
        $this->addSql('ALTER TABLE fournisseur CHANGE updated_user_id updated_user_id INT NOT NULL, CHANGE created_user_id created_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_bon_commande_frs CHANGE bon_commande_frs_id bon_commande_frs_id INT NOT NULL, CHANGE qte qte INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_bon_livraison CHANGE bon_livraison_id bon_livraison_id INT NOT NULL, CHANGE qte qte INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_bon_reception CHANGE bon_reception_id bon_reception_id INT NOT NULL, CHANGE qte qte INT NOT NULL');
        $this->addSql('ALTER TABLE ligne_devis CHANGE devis_id devis_id INT NOT NULL, CHANGE designation designation VARCHAR(400) NOT NULL, CHANGE qte qte INT NOT NULL, CHANGE remise remise NUMERIC(10, 3) NOT NULL, CHANGE ttc ttc NUMERIC(10, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_devis RENAME INDEX idx_888b2f1b41defada TO devis_id');
        $this->addSql('ALTER TABLE ligne_facture CHANGE facture_id facture_id INT NOT NULL, CHANGE designation designation VARCHAR(2000) NOT NULL, CHANGE qte qte INT NOT NULL, CHANGE remise remise NUMERIC(10, 3) NOT NULL, CHANGE benifice benifice NUMERIC(10, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_facture RENAME INDEX idx_611f5a297f2dee08 TO facture_id');
        $this->addSql('ALTER TABLE ligne_facture_avoir CHANGE facture_avoir_id facture_avoir_id INT NOT NULL, CHANGE qte qte INT NOT NULL, CHANGE qte_max qte_max INT DEFAULT NULL, CHANGE stock stock TINYINT(1) DEFAULT NULL, CHANGE reglement reglement TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_reglement CHANGE date_reglement date_reglement DATE NOT NULL');
        $this->addSql('ALTER TABLE ligne_reglement RENAME INDEX mode_reglement TO mode_reglement_id');
        $this->addSql('ALTER TABLE media CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE path path VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX reglement_id ON mouvement');
        $this->addSql('ALTER TABLE mouvement CHANGE total_retenu total_retenu DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE mouvement RENAME INDEX idx_5b51fc3ef2c56620 TO compte_id');
        $this->addSql('ALTER TABLE notification CHANGE updated_user_id updated_user_id INT NOT NULL, CHANGE created_user_id created_user_id INT NOT NULL');
        $this->addSql('ALTER TABLE retenu CHANGE taux taux DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE societe DROP INDEX UNIQ_19653DBDEA9FDD75, ADD INDEX media_id (media_id)');
        $this->addSql('ALTER TABLE societe DROP INDEX UNIQ_19653DBDF2C56620, ADD INDEX compte_id (compte_id)');
        $this->addSql('ALTER TABLE societe DROP FOREIGN KEY FK_19653DBDE104C1D3');
        $this->addSql('ALTER TABLE societe DROP FOREIGN KEY FK_19653DBDBB649746');
        $this->addSql('ALTER TABLE societe CHANGE created_user_id created_user_id INT NOT NULL, CHANGE updated_user_id updated_user_id INT NOT NULL, CHANGE rcs rcs VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE fax fax VARCHAR(255) DEFAULT NULL, CHANGE mobile mobile VARCHAR(255) DEFAULT NULL, CHANGE desactiver_photo desactiver_photo TINYINT(1) NOT NULL');
        $this->addSql('DROP INDEX famille_id ON sousfamille');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656607F2DEE08');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660D8D16068');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660D192482A');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656602A91441F');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660BB649746');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660E104C1D3');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566019EB6921');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B365660670C757F');
        $this->addSql('DROP INDEX fournisseur_id ON stock');
        $this->addSql('ALTER TABLE stock CHANGE type_doc type_doc VARCHAR(255) DEFAULT NULL, CHANGE qte qte INT NOT NULL');
        $this->addSql('CREATE INDEX fournisseur_id_3 ON stock (fournisseur_id)');
        $this->addSql('CREATE INDEX fournisseur_id_2 ON stock (fournisseur_id)');
        $this->addSql('CREATE INDEX fournisseur_id ON stock (client_id)');
        $this->addSql('ALTER TABLE stock RENAME INDEX idx_4b36566019eb6921 TO client_id');
        $this->addSql('ALTER TABLE stock RENAME INDEX idx_4b3656607f2dee08 TO facture_id');
        $this->addSql('ALTER TABLE stock RENAME INDEX idx_4b365660d192482a TO facture_avoir_id');
        $this->addSql('ALTER TABLE stock RENAME INDEX idx_4b365660d8d16068 TO bon_livraison_id');
        $this->addSql('ALTER TABLE stock RENAME INDEX idx_4b3656602a91441f TO bon_reception_id');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT stock_ibfk_2 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT stock_ibfk_3 FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (id)');
        $this->addSql('ALTER TABLE tva DROP FOREIGN KEY FK_EF699620BB649746');
        $this->addSql('ALTER TABLE tva DROP FOREIGN KEY FK_EF699620E104C1D3');
        $this->addSql('ALTER TABLE unite DROP FOREIGN KEY FK_1D64C118BB649746');
        $this->addSql('ALTER TABLE unite DROP FOREIGN KEY FK_1D64C118E104C1D3');
    }
}

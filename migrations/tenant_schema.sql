/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `article`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prix_achat` decimal(10,3) DEFAULT NULL,
  `marge` decimal(10,3) DEFAULT NULL,
  `prix_vente_ht` decimal(10,3) DEFAULT NULL,
  `tva_id` int(11) DEFAULT NULL,
  `prix_vente_ttc` decimal(10,3) DEFAULT NULL,
  `stockable` tinyint(1) NOT NULL,
  `service` tinyint(1) NOT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `qte_en_depart` int(11) DEFAULT NULL,
  `qte_en_stock` int(11) DEFAULT NULL,
  `seuil_alert` int(11) DEFAULT NULL,
  `date_ajout` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `desactiver_photo` tinyint(1) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `unite_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `famille_id` int(11) DEFAULT NULL,
  `sousfamille_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `tva_id` (`tva_id`),
  KEY `unite_id` (`unite_id`),
  KEY `categorie_id` (`categorie_id`),
  KEY `famille_id` (`famille_id`),
  KEY `sousfamille_id` (`sousfamille_id`),
  KEY `media_id` (`media_id`),
  KEY `fournisseur_id` (`fournisseur_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`tva_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `article_ibfk_10` FOREIGN KEY (`famille_id`) REFERENCES `famille` (`id`),
  CONSTRAINT `article_ibfk_2` FOREIGN KEY (`unite_id`) REFERENCES `unite` (`id`),
  CONSTRAINT `article_ibfk_3` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`),
  CONSTRAINT `article_ibfk_5` FOREIGN KEY (`sousfamille_id`) REFERENCES `sousfamille` (`id`),
  CONSTRAINT `article_ibfk_6` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `article_ibfk_7` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `article_ibfk_8` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`),
  CONSTRAINT `article_ibfk_9` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `banque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `created_user_id_2` (`created_user_id`),
  KEY `updated_user_id` (`updated_user_id`),
  CONSTRAINT `banque_ibfk_1` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `banque_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_commande_frs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bon_commande_frs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `termine` tinyint(1) NOT NULL,
  `date_creation` date DEFAULT NULL,
  `date_commande` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `fournisseur_id` (`fournisseur_id`),
  CONSTRAINT `bon_commande_frs_ibfk_1` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`),
  CONSTRAINT `bon_commande_frs_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `bon_commande_frs_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_livraison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bon_livraison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ht` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva` decimal(10,3) NOT NULL,
  `total` decimal(10,3) NOT NULL,
  `taux_retenu` decimal(10,3) NOT NULL,
  `total_retenu` decimal(10,3) NOT NULL,
  `regle` decimal(10,3) NOT NULL,
  `reste` decimal(10,3) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `termine` tinyint(1) NOT NULL,
  `converted` tinyint(1) NOT NULL,
  `date_creation` date DEFAULT NULL,
  `date_livraison` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `bon_livraison_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `bon_livraison_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `bon_livraisone_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bon_reception`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bon_reception` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bon_commande_id` int(11) DEFAULT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `ht` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva` decimal(10,3) NOT NULL,
  `total` decimal(10,3) NOT NULL,
  `taux_retenu` decimal(10,3) NOT NULL,
  `total_retenu` decimal(10,3) NOT NULL,
  `regle` decimal(10,3) NOT NULL,
  `reste` decimal(10,3) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `termine` tinyint(1) NOT NULL,
  `date_reception` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `fournisseur_id` (`fournisseur_id`),
  KEY `IDX_C77F2B94B4B54061` (`bon_commande_id`),
  CONSTRAINT `bon_reception_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `bon_reception_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `bon_reception_ibfk_4` FOREIGN KEY (`bon_commande_id`) REFERENCES `bon_commande_frs` (`id`),
  CONSTRAINT `bon_receptione_ibfk_1` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `categorie_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `categorie_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `passager` tinyint(1) NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `civilite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rs` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_postal` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `site_web` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `activite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remise` decimal(10,0) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `total_avoir_rembourse` decimal(10,3) NOT NULL,
  `total_avoir_non_rembourse` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `rc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `client_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `client_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `compte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rub` varchar(255) NOT NULL,
  `banque_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banque_id` (`banque_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `updated_user_id` (`updated_user_id`),
  CONSTRAINT `compte_ibfk_1` FOREIGN KEY (`banque_id`) REFERENCES `banque` (`id`),
  CONSTRAINT `compte_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `compte_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `devis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ht` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva` decimal(10,3) NOT NULL,
  `total` decimal(10,3) NOT NULL,
  `net_a_payer` decimal(10,3) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `termine` tinyint(1) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `date_validite` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `devis_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `devis_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ht` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva` decimal(10,3) NOT NULL,
  `total` decimal(10,3) NOT NULL,
  `taux_retenu` decimal(10,3) NOT NULL,
  `total_retenu` decimal(10,3) NOT NULL,
  `total_avoir_rembourse` decimal(10,3) NOT NULL,
  `total_avoir_non_rembourse` decimal(10,3) NOT NULL,
  `benifice` decimal(10,3) NOT NULL,
  `regle` decimal(10,3) NOT NULL,
  `reste` decimal(10,3) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `termine` tinyint(1) DEFAULT NULL,
  `from_bl` tinyint(1) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `timbre` decimal(10,3) NOT NULL DEFAULT '0.600',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `facture_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `facture_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `facture_avoir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facture_avoir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `facture_id` int(11) DEFAULT NULL,
  `ht` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva` decimal(10,3) NOT NULL,
  `total` decimal(10,3) NOT NULL,
  `benifice` decimal(10,3) DEFAULT NULL,
  `regle` decimal(10,3) DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `termine` tinyint(1) NOT NULL,
  `from_bl` tinyint(1) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `timbre` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `facture_id` (`facture_id`),
  CONSTRAINT `facture_avoir_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `facture_avoir_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `facture_avoir_ibfk_4` FOREIGN KEY (`facture_id`) REFERENCES `facture` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `famille`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `famille` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `IDX_2473F213BCF5E72D` (`categorie_id`),
  CONSTRAINT `famille_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `famille_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `famille_ibfk_3` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fournisseur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `rs` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mf` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresse3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code_postal` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pays` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `siteweb` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `tel2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `fournisseur_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `fournisseur_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_bon_commande_frs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_bon_commande_frs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bon_commande_frs_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qte` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `bon_commande_frs_id` (`bon_commande_frs_id`),
  CONSTRAINT `ligne_bon_commande_frs_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `ligne_bon_commande_frs_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_bon_commande_frs_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_bon_commande_frs_ibfk_5` FOREIGN KEY (`bon_commande_frs_id`) REFERENCES `bon_commande_frs` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_bon_livraison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_bon_livraison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bon_livraison_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qte` decimal(10,3) NOT NULL,
  `prix_unitaire` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva_id` int(11) DEFAULT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `tva_id` (`tva_id`),
  KEY `bon_livraison_id` (`bon_livraison_id`),
  CONSTRAINT `ligne_bon_livraison_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `ligne_bon_livraison_ibfk_2` FOREIGN KEY (`tva_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `ligne_bon_livraison_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_bon_livraison_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_bon_livraison_ibfk_5` FOREIGN KEY (`bon_livraison_id`) REFERENCES `bon_livraison` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_bon_reception`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_bon_reception` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bon_reception_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qte` decimal(10,3) NOT NULL,
  `prix_unitaire` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva_id` int(11) DEFAULT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `tva_id` (`tva_id`),
  KEY `bon_reception_id` (`bon_reception_id`),
  CONSTRAINT `ligne_bon_reception_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `ligne_bon_reception_ibfk_2` FOREIGN KEY (`tva_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `ligne_bon_reception_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_bon_reception_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_bon_reception_ibfk_5` FOREIGN KEY (`bon_reception_id`) REFERENCES `bon_reception` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_devis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_devis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `devis_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qte` decimal(10,3) NOT NULL,
  `prix_unitaire` decimal(10,3) NOT NULL,
  `remise` decimal(10,0) NOT NULL,
  `tva_id` int(11) DEFAULT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `tva_id` (`tva_id`),
  KEY `IDX_888B2F1B41DEFADA` (`devis_id`),
  CONSTRAINT `ligne_devis_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `ligne_devis_ibfk_2` FOREIGN KEY (`tva_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `ligne_devis_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_devis_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_devis_ibfk_5` FOREIGN KEY (`devis_id`) REFERENCES `devis` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=476 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_facture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facture_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `designation` varchar(2000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qte` decimal(10,3) NOT NULL,
  `prix_unitaire` decimal(10,3) NOT NULL,
  `remise` decimal(10,0) NOT NULL,
  `tva_id` int(11) DEFAULT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `benifice` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `tva_id` (`tva_id`),
  KEY `IDX_611F5A297F2DEE08` (`facture_id`),
  CONSTRAINT `ligne_facture_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `ligne_facture_ibfk_2` FOREIGN KEY (`tva_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `ligne_facture_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_facture_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_facture_ibfk_5` FOREIGN KEY (`facture_id`) REFERENCES `facture` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=516 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_facture_avoir`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_facture_avoir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facture_avoir_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `qte` decimal(10,3) NOT NULL,
  `qte_max` decimal(10,3) NOT NULL,
  `prix_unitaire` decimal(10,3) NOT NULL,
  `remise` decimal(10,3) NOT NULL,
  `tva_id` int(11) DEFAULT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `stock` tinyint(1) NOT NULL,
  `reglement` tinyint(1) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `tva_id` (`tva_id`),
  KEY `facture_avoir_id` (`facture_avoir_id`),
  CONSTRAINT `ligne_facture_avoir_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `ligne_facture_avoir_ibfk_2` FOREIGN KEY (`tva_id`) REFERENCES `tva` (`id`),
  CONSTRAINT `ligne_facture_avoir_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_facture_avoir_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_facture_avoir_ibfk_5` FOREIGN KEY (`facture_avoir_id`) REFERENCES `facture_avoir` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_reglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_reglement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facture_id` int(11) DEFAULT NULL,
  `reglement_id` int(11) DEFAULT NULL,
  `date_reglement` date DEFAULT NULL,
  `montant` decimal(10,3) NOT NULL,
  `mode_reglement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_echeance_cheque` date DEFAULT NULL,
  `num_cheque` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_echeance_traite` date DEFAULT NULL,
  `num_traite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compte_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `facture_id` (`facture_id`),
  KEY `reglement_id` (`reglement_id`),
  KEY `mode_reglement` (`mode_reglement`),
  KEY `updated_user_id_2` (`updated_user_id`),
  KEY `banque_rec_id` (`compte_id`),
  KEY `compte_id` (`compte_id`),
  KEY `compte_id_2` (`compte_id`),
  CONSTRAINT `ligne_reglement_ibfk_1` FOREIGN KEY (`facture_id`) REFERENCES `facture` (`id`),
  CONSTRAINT `ligne_reglement_ibfk_2` FOREIGN KEY (`reglement_id`) REFERENCES `reglement` (`id`),
  CONSTRAINT `ligne_reglement_ibfk_4` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_reglement_ibfk_5` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_reglement_ibfk_6` FOREIGN KEY (`compte_id`) REFERENCES `compte` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_reglement_bon_livraison`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_reglement_bon_livraison` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bon_livraison_id` int(11) DEFAULT NULL,
  `reglement_id` int(11) DEFAULT NULL,
  `date_reglement` date NOT NULL,
  `montant` decimal(10,3) NOT NULL,
  `mode_reglement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_echeance_cheque` date DEFAULT NULL,
  `num_cheque` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_echeance_traite` date DEFAULT NULL,
  `num_traite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compte_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `bon_livraison_id` (`bon_livraison_id`),
  KEY `reglement_id` (`reglement_id`),
  KEY `mode_reglement_id` (`mode_reglement`),
  KEY `updated_user_id_2` (`updated_user_id`),
  KEY `banque_rec_id` (`compte_id`),
  KEY `compte_id` (`compte_id`),
  KEY `compte_id_2` (`compte_id`),
  CONSTRAINT `ligne_reglement_bon_livraison_ibfk_1` FOREIGN KEY (`bon_livraison_id`) REFERENCES `bon_livraison` (`id`),
  CONSTRAINT `ligne_reglement_bon_livraison_ibfk_2` FOREIGN KEY (`reglement_id`) REFERENCES `reglement` (`id`),
  CONSTRAINT `ligne_reglement_bon_livraison_ibfk_4` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_reglement_bon_livraison_ibfk_5` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_reglement_bon_livraison_ibfk_6` FOREIGN KEY (`compte_id`) REFERENCES `compte` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ligne_reglement_bon_reception`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ligne_reglement_bon_reception` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bon_reception_id` int(11) DEFAULT NULL,
  `reglement_id` int(11) DEFAULT NULL,
  `date_reglement` date NOT NULL,
  `montant` decimal(10,3) NOT NULL,
  `mode_reglement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_echeance_cheque` date DEFAULT NULL,
  `num_cheque` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_echeance_traite` date DEFAULT NULL,
  `num_traite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compte_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `bon_reception_id` (`bon_reception_id`),
  KEY `reglement_id` (`reglement_id`),
  KEY `mode_reglement_id` (`mode_reglement`),
  KEY `updated_user_id_2` (`updated_user_id`),
  KEY `banque_rec_id` (`compte_id`),
  KEY `compte_id` (`compte_id`),
  KEY `compte_id_2` (`compte_id`),
  CONSTRAINT `ligne_reglement_bon_reception_ibfk_1` FOREIGN KEY (`bon_reception_id`) REFERENCES `bon_reception` (`id`),
  CONSTRAINT `ligne_reglement_bon_reception_ibfk_2` FOREIGN KEY (`reglement_id`) REFERENCES `reglement` (`id`),
  CONSTRAINT `ligne_reglement_bon_reception_ibfk_4` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_reglement_bon_reception_ibfk_5` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `ligne_reglement_bon_reception_ibfk_6` FOREIGN KEY (`compte_id`) REFERENCES `compte` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `magasin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `magasin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` text COLLATE utf8_unicode_ci,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `magasin_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `magasin_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `updated_user_id` (`updated_user_id`),
  CONSTRAINT `media_ibfk_1` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `media_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mode_reglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mode_reglement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `libelle` (`libelle`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `mode_reglement_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `mode_reglement_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mouvement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mouvement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mouvement` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_doc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `tier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `reglement_id` int(11) DEFAULT NULL,
  `mode_reglement` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_echeance` date DEFAULT NULL,
  `num_doc` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `etat` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compte_id` int(11) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `total_retenu` decimal(10,3) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reglement_id` (`reglement_id`),
  KEY `client_id` (`client_id`,`fournisseur_id`,`updated_user_id`,`created_user_id`),
  KEY `fournisseur_id` (`fournisseur_id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `IDX_5B51FC3EF2C56620` (`compte_id`),
  CONSTRAINT `mouvement_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `mouvement_ibfk_2` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`),
  CONSTRAINT `mouvement_ibfk_3` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `mouvement_ibfk_4` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `mouvement_ibfk_5` FOREIGN KEY (`compte_id`) REFERENCES `compte` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `value_id` int(11) NOT NULL,
  `vu` tinyint(1) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reglement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reglement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total` decimal(10,3) NOT NULL,
  `reste` decimal(10,3) NOT NULL,
  `note` text COLLATE utf8_unicode_ci NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `reglement_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `reglement_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `retenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taux` decimal(10,3) NOT NULL,
  `montant` decimal(10,3) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taux` (`taux`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `retenu_ibfk_1` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `retenu_ibfk_2` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `societe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `societe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rs` varchar(255) NOT NULL,
  `mf` varchar(255) NOT NULL,
  `rcs` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `pays` varchar(255) NOT NULL,
  `code_postale` varchar(255) NOT NULL,
  `tel` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `compte_id` int(11) DEFAULT NULL,
  `mobile` varchar(255) NOT NULL,
  `desactiver_photo` tinyint(1) DEFAULT NULL,
  `media_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_19653DBDF2C56620` (`compte_id`),
  UNIQUE KEY `UNIQ_19653DBDEA9FDD75` (`media_id`),
  KEY `user_created_id` (`created_user_id`),
  KEY `user_updated_id` (`updated_user_id`),
  CONSTRAINT `FK_19653DBDBB649746` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_19653DBDE104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `societe_ibfk_1` FOREIGN KEY (`compte_id`) REFERENCES `compte` (`id`),
  CONSTRAINT `societe_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sousfamille`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sousfamille` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `famille_id` int(11) DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `famille_id_2` (`famille_id`),
  KEY `famille_id` (`famille_id`),
  CONSTRAINT `sousfamille_ibfk_1` FOREIGN KEY (`famille_id`) REFERENCES `famille` (`id`),
  CONSTRAINT `sousfamille_ibfk_2` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `sousfamille_ibfk_3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type_doc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `facture_id` int(11) DEFAULT NULL,
  `bon_livraison_id` int(11) DEFAULT NULL,
  `facture_avoir_id` int(11) DEFAULT NULL,
  `bon_reception_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  `qte` decimal(10,3) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `mouvement` tinyint(1) NOT NULL,
  `ttc` decimal(10,3) NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `date_creation` date DEFAULT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `updated_user_id_2` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  KEY `article_id` (`article_id`),
  KEY `IDX_4B36566019EB6921` (`client_id`),
  KEY `IDX_4B3656607F2DEE08` (`facture_id`),
  KEY `IDX_4B365660D8D16068` (`bon_livraison_id`),
  KEY `IDX_4B365660D192482A` (`facture_avoir_id`),
  KEY `IDX_4B3656602A91441F` (`bon_reception_id`),
  KEY `fournisseur_id` (`fournisseur_id`),
  CONSTRAINT `FK_4B36566019EB6921` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  CONSTRAINT `FK_4B3656602A91441F` FOREIGN KEY (`bon_reception_id`) REFERENCES `bon_reception` (`id`),
  CONSTRAINT `FK_4B365660670C757F` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseur` (`id`),
  CONSTRAINT `FK_4B3656607F2DEE08` FOREIGN KEY (`facture_id`) REFERENCES `facture` (`id`),
  CONSTRAINT `FK_4B365660BB649746` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_4B365660D192482A` FOREIGN KEY (`facture_avoir_id`) REFERENCES `facture_avoir` (`id`),
  CONSTRAINT `FK_4B365660D8D16068` FOREIGN KEY (`bon_livraison_id`) REFERENCES `bon_livraison` (`id`),
  CONSTRAINT `FK_4B365660E104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `timbre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timbre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `valeur` decimal(10,3) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tva` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taux` decimal(10,0) NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taux` (`taux`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `FK_EF699620BB649746` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_EF699620E104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `unite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `updated_user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `updated_user_id` (`updated_user_id`),
  KEY `updated_user_id_2` (`updated_user_id`),
  KEY `created_user_id` (`created_user_id`),
  CONSTRAINT `FK_1D64C118BB649746` FOREIGN KEY (`updated_user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_1D64C118E104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D64992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_8D93D649A0D96FBF` (`email_canonical`),
  UNIQUE KEY `UNIQ_8D93D649C05FB297` (`confirmation_token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 24 août 2026 à 13:50
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `evenprod_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `acteurs`
--

DROP TABLE IF EXISTS `acteurs`;
CREATE TABLE IF NOT EXISTS `acteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `projet_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projet_id` (`projet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `acteurs`
--

INSERT INTO `acteurs` (`id`, `nom`, `prenom`, `date_naissance`, `contact`, `adresse`, `photo`, `cv_file`, `projet_id`) VALUES
(1, 'faye', 'eva', '1999-01-04', '784413400', 'Malicounda', 'photo_68aca6a198a505.13771587.jpg', 'cv_68aca6a1988341.60170568.pdf', NULL),
(2, 'diop', 'moussa', '2025-08-26', '785560099', 'mbour', 'photo_68add8689cdad5.36438724.jpg', 'cv_68add8689760b1.91565891.pdf', NULL),
(4, 'diop', 'El Hadji Madiop', '2026-08-21', '784413400', 'mbour', 'photo_6a8894f6b0cd65.21084206.jpg', '', NULL),
(5, 'Ba', 'Amadou ', '1995-02-23', '221774412344', 'Dakar', 'photo_6a8b615d312eb3.90202631.jpg', '', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `projet_id` int DEFAULT NULL,
  `nom` varchar(150) NOT NULL,
  `ninea` varchar(255) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `projet_id` (`projet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `projet_id`, `nom`, `ninea`, `contact`, `adresse`, `email`, `logo`, `created_at`) VALUES
(1, NULL, 'mayfay Global', 'AA23456789JJ', '784413488', 'saly', 'diopelhadjimadiop@gmail.com', 'logo_1756493095.jpg', '2025-08-29 18:52:59'),
(3, NULL, 'ASS APPLE', 'AA23456789JJ', '784413400', 'DAKAR', 'elhadji1.diop@uadb.edu.sn', 'logo_1787344707.jpg', '2026-08-21 20:38:27');

-- --------------------------------------------------------

--
-- Structure de la table `depenses`
--

DROP TABLE IF EXISTS `depenses`;
CREATE TABLE IF NOT EXISTS `depenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `serie_id` int NOT NULL,
  `tournage_id` int DEFAULT NULL,
  `acteur_id` int NOT NULL,
  `libelle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_depense` date NOT NULL,
  `type_depense` varchar(100) DEFAULT NULL,
  `beneficiaire` varchar(150) DEFAULT NULL,
  `telephone_beneficiaire` varchar(20) DEFAULT NULL,
  `justificatif` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`),
  KEY `tournage_id` (`tournage_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `depenses`
--

INSERT INTO `depenses` (`id`, `serie_id`, `tournage_id`, `acteur_id`, `libelle`, `montant`, `date_depense`, `type_depense`, `beneficiaire`, `telephone_beneficiaire`, `justificatif`) VALUES
(1, 1, 2, 2, NULL, 45000.00, '2025-09-10', 'reglement_acteur', NULL, NULL, NULL),
(5, 1, 2, 0, 'azertyuiop', 12000.00, '2025-08-29', 'Transport', NULL, NULL, 'depense_1756490096.pdf'),
(7, 1, 5, 2, NULL, 45000.00, '2025-09-09', 'reglement_acteur', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `designation`
--

DROP TABLE IF EXISTS `designation`;
CREATE TABLE IF NOT EXISTS `designation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facture_id` int NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `quantite` int DEFAULT NULL,
  `prix_unitaire` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT '',
  `montant` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facture_id` (`facture_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `designation`
--

INSERT INTO `designation` (`id`, `facture_id`, `libelle`, `quantite`, `prix_unitaire`, `montant`) VALUES
(1, 2, 'sponsoring fassema', 8, 'forfait', 1400000),
(2, 2, 'TFM', 0, '', 0),
(3, 2, 'banniere', 7, '', 0),
(7, 6, 'sponsoring fassema', 0, NULL, 120000),
(8, 6, 'palcement', 5, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `serie_id` int NOT NULL,
  `reference` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_facture` date NOT NULL,
  `description` text,
  `total` float DEFAULT NULL,
  `type` varchar(60) NOT NULL DEFAULT 'devis',
  `date_validation` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `client_id` (`client_id`),
  KEY `serie_id` (`serie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `factures`
--

INSERT INTO `factures` (`id`, `client_id`, `serie_id`, `reference`, `date_facture`, `description`, `total`, `type`, `date_validation`) VALUES
(2, 1, 1, 'REF-25-001', '2025-08-31', 'ass bamba test', 1400000, 'Facture', '2025-08-31'),
(6, 1, 1, 'REF-25-002', '2025-09-09', 'test3', 120000, 'devis', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facture_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference` varchar(255) DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `piece_jointe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facture_id` (`facture_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `facture_id`, `montant`, `date_paiement`, `reference`, `type`, `piece_jointe`) VALUES
(1, 2, 800000.00, '2025-09-06 10:16:47', 'PAY-25-001', 'Espèces', '1756901879_4668.pdf'),
(3, 2, 40000.00, '2026-08-21 21:17:17', 'PAY-26-002', 'Virement bancaire', '1787347060_gagne1.pdf');

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

DROP TABLE IF EXISTS `projets`;
CREATE TABLE IF NOT EXISTS `projets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `ninea` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`id`, `nom`, `logo`, `contact`, `localisation`, `ninea`, `created_at`) VALUES
(1, 'evenprod', 'logo.jpg', '339087788', 'dakar ucad', 'A-233738NN-6373', '2025-08-25 16:54:33');

-- --------------------------------------------------------

--
-- Structure de la table `salaries`
--

DROP TABLE IF EXISTS `salaries`;
CREATE TABLE IF NOT EXISTS `salaries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `adresse` text,
  `date_naissance` date NOT NULL,
  `fonction` varchar(100) NOT NULL,
  `date_embauche` date NOT NULL,
  `type_contrat` enum('cdi','cdd','stage') NOT NULL,
  `salaire` decimal(15,2) NOT NULL,
  `statut` enum('actif','inactif','en_conge') NOT NULL DEFAULT 'actif',
  `photo` varchar(255) DEFAULT NULL,
  `contrat` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_statut` (`statut`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `salaries`
--

INSERT INTO `salaries` (`id`, `nom`, `prenom`, `telephone`, `email`, `adresse`, `date_naissance`, `fonction`, `date_embauche`, `type_contrat`, `salaire`, `statut`, `photo`, `contrat`, `created_at`, `updated_at`) VALUES
(1, 'diop', 'Ass', '784413400', 'diopelhadjimadiop@gmail.com', 'mbour', '2026-08-22', 'ingenieur', '2026-08-22', 'cdi', 300000.00, 'actif', '6a8a170887cc3.jpeg', '6a8a1708883c9.pdf', '2026-08-22 21:39:20', '2026-08-23 12:28:19');

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

DROP TABLE IF EXISTS `series`;
CREATE TABLE IF NOT EXISTS `series` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `description` text,
  `logo` varchar(255) DEFAULT NULL,
  `date_sortie` date DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `budget` decimal(10,0) NOT NULL,
  `transport` decimal(15,2) NOT NULL DEFAULT '0.00',
  `decors` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reglement_acteurs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `accessoires` decimal(15,2) NOT NULL DEFAULT '0.00',
  `hmc` decimal(15,2) NOT NULL DEFAULT '0.00',
  `carburant` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pharmacie` decimal(15,2) NOT NULL DEFAULT '0.00',
  `receptions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `autres_achats` decimal(15,2) NOT NULL DEFAULT '0.00',
  `projet_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projet_id` (`projet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `series`
--

INSERT INTO `series` (`id`, `titre`, `description`, `logo`, `date_sortie`, `type`, `budget`, `transport`, `decors`, `reglement_acteurs`, `accessoires`, `hmc`, `carburant`, `pharmacie`, `receptions`, `autres_achats`, `projet_id`) VALUES
(1, 'xalisso', 'serie senegalaise....', 'serie_68c1b47a6063a8.68914704.jpg', NULL, 'Série TV', 3000000, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL),
(3, 'Babel Ass', 'azertyuiop', 'serie_68b9dd43868667.16908433.jpg', NULL, 'Série TV', 2000000, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `serie_acteur`
--

DROP TABLE IF EXISTS `serie_acteur`;
CREATE TABLE IF NOT EXISTS `serie_acteur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `serie_id` int NOT NULL,
  `acteur_id` int NOT NULL,
  `personnage` varchar(100) DEFAULT NULL,
  `cachet` decimal(10,2) NOT NULL DEFAULT '0.00',
  `type_acteur` enum('forfaitaire','journalier') DEFAULT 'forfaitaire',
  `contrat` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`),
  KEY `acteur_id` (`acteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `serie_acteur`
--

INSERT INTO `serie_acteur` (`id`, `serie_id`, `acteur_id`, `personnage`, `cachet`, `type_acteur`, `contrat`, `role`) VALUES
(3, 1, 2, NULL, 45000.00, 'forfaitaire', NULL, NULL),
(5, 3, 1, NULL, 400000.00, 'forfaitaire', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `tournages`
--

DROP TABLE IF EXISTS `tournages`;
CREATE TABLE IF NOT EXISTS `tournages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `serie_id` int NOT NULL,
  `date_tournage` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `description` text,
  `reference` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `serie_id` (`serie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tournages`
--

INSERT INTO `tournages` (`id`, `serie_id`, `date_tournage`, `date_fin`, `lieu`, `description`, `reference`) VALUES
(2, 1, '2025-09-10', NULL, NULL, NULL, 'RF-25-002'),
(5, 1, '2025-09-09', NULL, NULL, NULL, 'RF-25-003');

-- --------------------------------------------------------

--
-- Structure de la table `tournage_acteur`
--

DROP TABLE IF EXISTS `tournage_acteur`;
CREATE TABLE IF NOT EXISTS `tournage_acteur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournage_id` int NOT NULL,
  `acteur_id` int DEFAULT NULL,
  `sequence` int NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tournage_id` (`tournage_id`),
  KEY `acteur_id` (`acteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `tournage_acteur`
--

INSERT INTO `tournage_acteur` (`id`, `tournage_id`, `acteur_id`, `sequence`, `role`) VALUES
(7, 2, 2, 0, NULL),
(8, 5, 2, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `statut` tinyint(1) DEFAULT '1',
  `updated` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `telephone`, `profile`, `role`, `statut`, `updated`, `created_at`) VALUES
(1, 'diop', 'elhadji', 'diopelhadjimadiop@gmail.com', '832f512c2ac787e37a040a9fc6d43545788599c8', '784413400', 'profil_1757174719.jpg', 'admin', 1, 1, '2025-08-25 16:50:17'),
(5, 'sylla', 'babacar_chef', 'sylla@gmail.com', '9ead80632f1a0ff63cc214fa50b034ae7f48dde4', '789904533', 'profil_1757177295.jpg', 'caisse', 1, 1, '2025-09-06 16:48:15'),
(7, 'DIOP', 'Ass', 'elhadji1.diop@uadb.edu.sn', '25baf953526b78bc58888e6239492afee87b80d0', '221784413400', 'profil_6a8c33154d198.jpeg', 'comptable', 1, 0, '2026-08-24 12:03:33');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `acteurs`
--
ALTER TABLE `acteurs`
  ADD CONSTRAINT `acteurs_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `depenses`
--
ALTER TABLE `depenses`
  ADD CONSTRAINT `depenses_ibfk_1` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `depenses_ibfk_2` FOREIGN KEY (`tournage_id`) REFERENCES `tournages` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `designation`
--
ALTER TABLE `designation`
  ADD CONSTRAINT `designation_ibfk_1` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `factures`
--
ALTER TABLE `factures`
  ADD CONSTRAINT `factures_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `factures_ibfk_2` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`facture_id`) REFERENCES `factures` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `series_ibfk_1` FOREIGN KEY (`projet_id`) REFERENCES `projets` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `serie_acteur`
--
ALTER TABLE `serie_acteur`
  ADD CONSTRAINT `serie_acteur_ibfk_1` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `serie_acteur_ibfk_2` FOREIGN KEY (`acteur_id`) REFERENCES `acteurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tournages`
--
ALTER TABLE `tournages`
  ADD CONSTRAINT `tournages_ibfk_1` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tournage_acteur`
--
ALTER TABLE `tournage_acteur`
  ADD CONSTRAINT `tournage_acteur_ibfk_1` FOREIGN KEY (`tournage_id`) REFERENCES `tournages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournage_acteur_ibfk_2` FOREIGN KEY (`acteur_id`) REFERENCES `acteurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

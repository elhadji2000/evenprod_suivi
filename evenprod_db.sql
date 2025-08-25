-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 25 août 2025 à 16:42
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
  `projet_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projet_id` (`projet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `projet_id` int NOT NULL,
  `nom` varchar(150) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projet_id` (`projet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `depenses`
--

DROP TABLE IF EXISTS `depenses`;
CREATE TABLE IF NOT EXISTS `depenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `serie_id` int NOT NULL,
  `tournage_id` int DEFAULT NULL,
  `libelle` varchar(255) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_depense` date NOT NULL,
  `type_depense` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`),
  KEY `tournage_id` (`tournage_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `designation`
--

DROP TABLE IF EXISTS `designation`;
CREATE TABLE IF NOT EXISTS `designation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facture_id` int NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `quantite` int NOT NULL DEFAULT '1',
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `facture_id` (`facture_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `serie_id` int NOT NULL,
  `reference` varchar(50) NOT NULL,
  `date_facture` date NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `client_id` (`client_id`),
  KEY `serie_id` (`serie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `facture_id` int NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` date NOT NULL,
  `type` enum('especes','virement','cheque','carte') DEFAULT 'especes',
  PRIMARY KEY (`id`),
  KEY `facture_id` (`facture_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `projet_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projet_id` (`projet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`id`),
  KEY `serie_id` (`serie_id`),
  KEY `acteur_id` (`acteur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournages`
--

DROP TABLE IF EXISTS `tournages`;
CREATE TABLE IF NOT EXISTS `tournages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `serie_id` int NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `description` text,
  `reference` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `serie_id` (`serie_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tournage_acteur`
--

DROP TABLE IF EXISTS `tournage_acteur`;
CREATE TABLE IF NOT EXISTS `tournage_acteur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tournage_id` int NOT NULL,
  `acteur_id` int NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tournage_id` (`tournage_id`),
  KEY `acteur_id` (`acteur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `role` enum('admin','manager') DEFAULT 'manager',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 03 déc. 2025 à 14:30
-- Version du serveur : 8.2.0
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `grade_management`
--

-- --------------------------------------------------------

--
-- Structure de la table `ecues`
--

DROP TABLE IF EXISTS `ecues`;
CREATE TABLE IF NOT EXISTS `ecues` (
  `id` varchar(10) NOT NULL,
  `ue_id` varchar(10) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `coef` float NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ue_id` (`ue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ecues`
--

INSERT INTO `ecues` (`id`, `ue_id`, `nom`, `coef`) VALUES
('uef311', 'uef310', 'ECUEF311 – Probabilité et statistique', 2),
('uef321', 'uef320', 'ECUEF321 – Théorie des langages et Automates', 1),
('uef322', 'uef320', 'ECUEF322 – Graphes et optimisation', 1),
('uef331', 'uef330', 'ECUEF331 – Conception des Systèmes d\'Information', 1.5),
('uef332', 'uef330', 'ECUEF332 – Programmation Java', 2),
('uef341', 'uef340', 'ECUEF341 – Ingénierie des Bases de Données', 1.5),
('uef342', 'uef340', 'ECUEF342 – Services des Réseaux', 1),
('ueo311', 'ueo310', 'ECUEO311 – Génie Logiciel', 1.5),
('ueo312', 'ueo310', 'ECUEO312 – Design Graphique', 1.5),
('uet311', 'uet310', 'ECUET311 – Anglais 3', 1),
('uet312', 'uet310', 'ECUET312 – Gestion d\'entreprise', 1);

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `ecue_id` varchar(10) NOT NULL,
  `cc` float DEFAULT NULL,
  `tp` float DEFAULT NULL,
  `examen` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_ecue` (`user_id`,`ecue_id`),
  KEY `ecue_id` (`ecue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `grades`
--

INSERT INTO `grades` (`id`, `user_id`, `ecue_id`, `cc`, `tp`, `examen`) VALUES
(1, 1, 'uef311', 11, 7, 20),
(2, 1, 'uef321', 11, 11, 1),
(3, 1, 'uef322', NULL, 11, 1),
(4, 1, 'uef331', NULL, NULL, NULL),
(5, 1, 'uef332', NULL, NULL, NULL),
(6, 1, 'uef341', NULL, NULL, NULL),
(7, 1, 'uef342', NULL, NULL, NULL),
(8, 1, 'ueo311', NULL, NULL, NULL),
(9, 1, 'ueo312', 11, 11, 11),
(10, 2, 'uef311', 11, 11, 11),
(11, 2, 'uef321', NULL, NULL, NULL),
(12, 2, 'uef322', NULL, NULL, NULL),
(13, 2, 'uef331', NULL, NULL, NULL),
(14, 2, 'uef332', NULL, NULL, NULL),
(15, 2, 'uef341', NULL, NULL, NULL),
(16, 2, 'uef342', NULL, NULL, NULL),
(17, 2, 'ueo311', NULL, NULL, NULL),
(18, 2, 'ueo312', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ues`
--

DROP TABLE IF EXISTS `ues`;
CREATE TABLE IF NOT EXISTS `ues` (
  `id` varchar(10) NOT NULL,
  `code` varchar(10) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `credit` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ues`
--

INSERT INTO `ues` (`id`, `code`, `nom`, `credit`) VALUES
('uef310', 'UEF310', 'Probabilité', 4),
('uef320', 'UEF320', 'Automates et Optimisation', 4),
('uef330', 'UEF330', 'CPOO', 7),
('uef340', 'UEF340', 'Bases de données et Réseaux', 5),
('ueo310', 'UEO310', 'Unité optionnelle', 6),
('uet310', 'UET310', 'Langue et Culture d\'Entreprise', 4);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'BLACK MASK', 'ghanmiyoussef39@gmail.com', '$2y$10$MbeU0lZGelrq/WC8I2.xTuElym8aIg3aNcv3qOURUahI49mepaJry', '2025-11-21 04:36:54'),
(2, 'BLACK MASK', 'ghanmiyoussef90@gmail.com', '$2y$10$STEWaBHNeDv2QkB1t7esveIPai.dX0q9e0iBEgN0WpOiM1t5WYxfS', '2025-11-21 09:43:58');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ecues`
--
ALTER TABLE `ecues`
  ADD CONSTRAINT `ecues_ibfk_1` FOREIGN KEY (`ue_id`) REFERENCES `ues` (`id`);

--
-- Contraintes pour la table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`ecue_id`) REFERENCES `ecues` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

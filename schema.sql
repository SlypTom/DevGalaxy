-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8.remi
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 14 juin 2026 à 21:17
-- Version du serveur : 8.0.43
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Q230181`
--

-- --------------------------------------------------------

--
-- Structure de la table `web2026_Categorie`
--

CREATE TABLE `web2026_Categorie` (
                                     `cid` int NOT NULL,
                                     `intitule` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `web2026_Categorie`
--

INSERT INTO `web2026_Categorie` (`cid`, `intitule`) VALUES
                                                        (1, 'Backend & Sécurité'),
                                                        (2, 'Frontend & Design'),
                                                        (3, 'Intelligence Artificielle'),
                                                        (4, 'Événement Général');

-- --------------------------------------------------------

--
-- Structure de la table `web2026_Prestation`
--

CREATE TABLE `web2026_Prestation` (
                                      `pid` int NOT NULL,
                                      `intitule` varchar(255) NOT NULL,
                                      `description` text NOT NULL,
                                      `image` varchar(255) DEFAULT 'default.png',
                                      `categorie_id` int NOT NULL,
                                      `artiste_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `web2026_Prestation`
--

INSERT INTO `web2026_Prestation` (`pid`, `intitule`, `description`, `image`, `categorie_id`, `artiste_id`) VALUES
                                                                                                               (1, 'Keynote : L\'Odyssée du Code', 'Ouverture des portes & café gravitationnel.', 'default.png', 4, 1),
                                                                                                               (2, 'Débat : Tabs vs Spaces', 'Le combat final pour trancher la question. Une discussion animée sur les standards d\'indentation dans la galaxie.', 'presta_6a2f123eb553c.jpg', 2, 2),
                                                                                                               (3, 'Survie en ligne de commande', 'Les commandes Bash essentielles pour ne pas se perdre dans le vide intersidéral de votre terminal.', 'presta_6a2f12a669306.png', 1, 3),
                                                                                                               (4, 'Atelier UX/UI Intergalactique', 'Créer des interfaces accessibles pour toutes les espèces. Les bonnes pratiques du design universel.', 'presta_6a2f12fcad3a3.jpg', 2, 3),
                                                                                                               (5, 'Pause Déjeuner', 'Ravitaillement au Space Buffet.', 'default.png', 4, 1),
                                                                                                               (6, 'Injections SQL en Apesanteur', 'Protéger vos bases de données contre les attaques. Architecture blindée garantie.', 'presta_6a2f11fea3560.png', 1, 2),
                                                                                                               (7, 'I.A. et Conscience', 'Jusqu\'où peut aller l\'apprentissage automatique ? Une réflexion sur les limites de l\'intelligence.', 'presta_6a2f138331f42.png', 3, 4),
                                                                                                               (8, 'Clôture & Apéro de l\'espace', 'Fin de l\'évènement !', 'default.png', 4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `web2026_Programmation`
--

CREATE TABLE `web2026_Programmation` (
                                         `prog_id` int NOT NULL,
                                         `prestation_id` int NOT NULL,
                                         `scene_id` int NOT NULL,
                                         `heure_debut` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `web2026_Programmation`
--

INSERT INTO `web2026_Programmation` (`prog_id`, `prestation_id`, `scene_id`, `heure_debut`) VALUES
                                                                                                (2, 4, 2, '11:00:00'),
                                                                                                (3, 2, 1, '12:00:00'),
                                                                                                (4, 5, 3, '13:00:00'),
                                                                                                (5, 3, 1, '14:00:00'),
                                                                                                (6, 7, 2, '15:00:00'),
                                                                                                (7, 4, 3, '16:00:00'),
                                                                                                (8, 8, 1, '18:00:00'),
                                                                                                (9, 6, 2, '17:00:00'),
                                                                                                (10, 1, 1, '10:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `web2026_Scene`
--

CREATE TABLE `web2026_Scene` (
                                 `sid` int NOT NULL,
                                 `nom_scene` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `web2026_Scene`
--

INSERT INTO `web2026_Scene` (`sid`, `nom_scene`) VALUES
                                                     (1, 'Salle Jupiter'),
                                                     (2, 'Salle Mars'),
                                                     (3, 'Salle Saturne');

-- --------------------------------------------------------

--
-- Structure de la table `web2026_Utilisateur`
--

CREATE TABLE `web2026_Utilisateur` (
                                       `uid` int NOT NULL,
                                       `nom` varchar(100) NOT NULL,
                                       `prenom` varchar(100) NOT NULL,
                                       `nom_artiste` varchar(100) DEFAULT NULL,
                                       `email` varchar(255) NOT NULL,
                                       `mot_passe_hashe` varchar(255) NOT NULL,
                                       `description` text,
                                       `photo` varchar(255) DEFAULT NULL,
                                       `est_organisateur` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `web2026_Utilisateur`
--

INSERT INTO `web2026_Utilisateur` (`uid`, `nom`, `prenom`, `nom_artiste`, `email`, `mot_passe_hashe`, `description`, `photo`, `est_organisateur`) VALUES
                                                                                                                                                      (1, 'Admin', 'DevGalaxy', 'SuperAdmin', 'admin@devgalaxy.com', 'Admin123', NULL, NULL, 1),
                                                                                                                                                      (2, 'Dupont', 'Sarah', 'StarCoder', 'sarah@devgalaxy.com', 'Sarah123', 'Développeuse full-stack passionnée par le web moderne et l\'architecture cloud.', NULL, 0),
                                                                                                                                                      (3, 'Martin', 'Dave', 'DataDave', 'dave@devgalaxy.com', 'Dave123', 'Expert en data science et intelligence artificielle, speaker régulier dans les conférences tech.', NULL, 0),
                                                                                                                                                      (4, 'Leclerc', 'Hal', 'HalTheBuilder', 'hal@devgalaxy.com', 'Hal123', 'Architecte logiciel spécialisé en systèmes distribués et DevOps.', NULL, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `web2026_Categorie`
--
ALTER TABLE `web2026_Categorie`
    ADD PRIMARY KEY (`cid`);

--
-- Index pour la table `web2026_Prestation`
--
ALTER TABLE `web2026_Prestation`
    ADD PRIMARY KEY (`pid`),
    ADD KEY `categorie_id` (`categorie_id`),
    ADD KEY `artiste_id` (`artiste_id`);

--
-- Index pour la table `web2026_Programmation`
--
ALTER TABLE `web2026_Programmation`
    ADD PRIMARY KEY (`prog_id`),
    ADD UNIQUE KEY `contrainte_scene_heure` (`scene_id`,`heure_debut`),
    ADD KEY `prestation_id` (`prestation_id`);

--
-- Index pour la table `web2026_Scene`
--
ALTER TABLE `web2026_Scene`
    ADD PRIMARY KEY (`sid`);

--
-- Index pour la table `web2026_Utilisateur`
--
ALTER TABLE `web2026_Utilisateur`
    ADD PRIMARY KEY (`uid`),
    ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `web2026_Categorie`
--
ALTER TABLE `web2026_Categorie`
    MODIFY `cid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `web2026_Prestation`
--
ALTER TABLE `web2026_Prestation`
    MODIFY `pid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `web2026_Programmation`
--
ALTER TABLE `web2026_Programmation`
    MODIFY `prog_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `web2026_Scene`
--
ALTER TABLE `web2026_Scene`
    MODIFY `sid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `web2026_Utilisateur`
--
ALTER TABLE `web2026_Utilisateur`
    MODIFY `uid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `web2026_Prestation`
--
ALTER TABLE `web2026_Prestation`
    ADD CONSTRAINT `web2026_Prestation_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `web2026_Categorie` (`cid`),
    ADD CONSTRAINT `web2026_Prestation_ibfk_2` FOREIGN KEY (`artiste_id`) REFERENCES `web2026_Utilisateur` (`uid`);

--
-- Contraintes pour la table `web2026_Programmation`
--
ALTER TABLE `web2026_Programmation`
    ADD CONSTRAINT `web2026_Programmation_ibfk_1` FOREIGN KEY (`prestation_id`) REFERENCES `web2026_Prestation` (`pid`),
    ADD CONSTRAINT `web2026_Programmation_ibfk_2` FOREIGN KEY (`scene_id`) REFERENCES `web2026_Scene` (`sid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- 2. Table des Utilisateurs (Administrateurs pour le Back-office)
CREATE TABLE web2026_users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       email VARCHAR(100) NOT NULL UNIQUE,
                       password VARCHAR(255) NOT NULL, -- Dans un vrai projet, le mot de passe doit être hashé !
                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Table des Salles (Rooms)
CREATE TABLE web2026_rooms (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       name VARCHAR(50) NOT NULL,
                       capacity INT NOT NULL,
                       description TEXT
);

-- 4. Table des Conférenciers (Speakers)
CREATE TABLE web2026_speakers (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          name VARCHAR(100) NOT NULL,
                          role VARCHAR(100) NOT NULL, -- Ex: Lead Security Engineer
                          specialty VARCHAR(100),     -- Ex: Backend, Design, IA
                          bio TEXT,
                          citation VARCHAR(255),
                          fun_fact VARCHAR(255),
                          image_url VARCHAR(255)      -- Nom du fichier image (ex: speaker_sarah.jpg)
);

-- 5. Table des Événements / Conférences (Events)
CREATE TABLE web2026_events (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(150) NOT NULL,
                        description TEXT,
                        start_time DATETIME NOT NULL,
                        end_time DATETIME NOT NULL,
                        room_id INT,    -- Clé étrangère vers la salle
                        speaker_id INT, -- Clé étrangère vers le speaker

    -- Définition des relations (Foreign Keys)
                        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL,
                        FOREIGN KEY (speaker_id) REFERENCES speakers(id) ON DELETE SET NULL
);

-- ==========================================
-- INSERTION DES DONNÉES (SEEDING)
-- ==========================================

-- A. Création d'un Admin par défaut
-- Login: admin / Mot de passe: admin123
INSERT INTO web2026_users (username, email, password) VALUES
    ('admin', 'admin@devgalaxy.com', 'admin123');

-- B. Insertion des Salles
INSERT INTO web2026_rooms (name, capacity, description) VALUES
                                                    ('Salle Jupiter', 200, 'La grande scène principale pour les Keynotes.'),
                                                    ('Salle Mars', 50, 'Une salle intimiste pour les ateliers pratiques.'),
                                                    ('Lounge Saturne', 100, 'Espace détente et réseautage.');

-- C. Insertion des Speakers (Avec les bios validées)
INSERT INTO web2026_speakers (name, role, specialty, bio, citation, fun_fact, image_url) VALUES
                                                                                     (
                                                                                         'Sarah "Loop" Connor',
                                                                                         'Lead Security Engineer',
                                                                                         'Cybersécurité & Backend',
                                                                                         'Ancienne White Hat reconvertie dans la protection des infrastructures critiques, Sarah a passé les dix dernières années à sécuriser des banques et des gouvernements. Elle est connue pour avoir stoppé une attaque majeure avec un simple script Python.',
                                                                                         'Si votre mot de passe est 123456, vous méritez d''être hacké.',
                                                                                         'Elle code sur un clavier mécanique sans lettres imprimées.',
                                                                                         'speaker_sarah.jpg'
                                                                                     ),
                                                                                     (
                                                                                         'Dave "Pixel" Bowman',
                                                                                         'Creative Director',
                                                                                         'Frontend & Design Systems',
                                                                                         'Dave ne voit pas le monde en atomes, mais en composants React. Obsédé par la performance et l''accessibilité, il milite pour un web plus léger et plus beau. Auteur du best-seller "Le Zen et l''art du CSS".',
                                                                                         'Une div non fermée est une porte ouverte vers le chaos.',
                                                                                         'Il porte toujours des chaussettes de la couleur hexadécimale de son projet.',
                                                                                         'speaker_dave.jpg'
                                                                                     ),
                                                                                     (
                                                                                         'H.A.L. 9000',
                                                                                         'IA Autonome v9.0',
                                                                                         'Intelligence Artificielle',
                                                                                         'H.A.L. est la première IA invitée en tant que conférencière principale. Elle analyse des téraoctets de code par seconde pour prédire les bugs avant même que vous ne les écriviez. Elle promet de rester pacifique.',
                                                                                         'Je suis désolé Dave, je ne peux pas merger cette branche.',
                                                                                         'Elle a gagné un tournoi d''échecs contre ses créateurs en 0.04 seconde.',
                                                                                         'speaker_hal.jpg'
                                                                                     );

-- D. Insertion du Programme (Events)
-- Note: Les ID des salles et speakers correspondent à l'ordre d'insertion ci-dessus.
-- Date fixée au 15 Novembre 2025

INSERT INTO web2026_events (title, description, start_time, end_time, room_id, speaker_id) VALUES
                                                                                       (
                                                                                           'Keynote : L''Odyssée du Code',
                                                                                           'Ouverture des portes, café gravitationnel et présentation des enjeux de 2025.',
                                                                                           '2025-11-15 09:30:00', '2025-11-15 10:15:00',
                                                                                           1, -- Salle Jupiter
                                                                                           NULL -- Pas de speaker unique (c'est l'équipe)
                                                                                       ),
                                                                                       (
                                                                                           'Injections SQL en Apesanteur',
                                                                                           'Comment protéger vos bases de données contre les attaques modernes. Démo live.',
                                                                                           '2025-11-15 10:30:00', '2025-11-15 11:30:00',
                                                                                           1, -- Salle Jupiter
                                                                                           1  -- Sarah Connor
                                                                                       ),
                                                                                       (
                                                                                           'CSS Grid : Alignez vos planètes',
                                                                                           'Fini les float: left. Apprenez à créer des mises en page qui défient la gravité.',
                                                                                           '2025-11-15 11:45:00', '2025-11-15 12:45:00',
                                                                                           2, -- Salle Mars
                                                                                           2  -- Dave Bowman
                                                                                       ),
                                                                                       (
                                                                                           'Pause Déjeuner & Networking',
                                                                                           'Ravitaillement au Space Buffet.',
                                                                                           '2025-11-15 13:00:00', '2025-11-15 14:15:00',
                                                                                           3, -- Lounge Saturne
                                                                                           NULL
                                                                                       ),
                                                                                       (
                                                                                           'L''IA va-t-elle vous remplacer ?',
                                                                                           'Analyse froide et logique du futur des développeurs juniors face aux assistants de code.',
                                                                                           '2025-11-15 14:30:00', '2025-11-15 15:30:00',
                                                                                           1, -- Salle Jupiter
                                                                                           3  -- H.A.L.
                                                                                       ),
                                                                                       (
                                                                                           'Débat : Tabs vs Spaces',
                                                                                           'Le combat final. Un débat sanglant pour trancher définitivement la question.',
                                                                                           '2025-11-15 16:00:00', '2025-11-15 17:00:00',
                                                                                           2, -- Salle Mars
                                                                                           1 -- Sarah (modératrice principale)
                                                                                       );
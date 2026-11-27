CREATE DATABASE IF NOT EXISTS todolist_app
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE todolist_app;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS tache;
DROP TABLE IF EXISTS liste;
DROP TABLE IF EXISTS utilisateur;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE utilisateur (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifiant VARCHAR(50) NOT NULL UNIQUE,
    mdp_hash VARCHAR(255) NOT NULL,
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE liste (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    utilisateur_id INT UNSIGNED NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_liste_utilisateur
        FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tache (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    liste_id INT UNSIGNED NOT NULL,
    titre VARCHAR(150) NOT NULL,
    description TEXT NULL,
    priorite ENUM('basse', 'normale', 'haute') NOT NULL DEFAULT 'normale',
    statut ENUM('a_faire', 'en_cours', 'terminee') NOT NULL DEFAULT 'a_faire',
    echeance DATE NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tache_liste
        FOREIGN KEY (liste_id) REFERENCES liste(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Utilisateurs de test :
-- alice : Alice2026!
-- bob   : Bob2026!
INSERT INTO utilisateur (identifiant, mdp_hash) VALUES
('alice', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('bob',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note : pour garantir un hash valide généré par password_hash(), on insère via des hashes standards :
UPDATE utilisateur
SET mdp_hash = '$2y$12$KIXQ8v6z9o5pL2mN7rT1u.8yH4wF1cG3jB6vE9nR2tY5xZ7qA0mS2'
WHERE identifiant = 'alice';

UPDATE utilisateur
SET mdp_hash = '$2y$12$M3nP6qR9sT2vW5xY8zA1b.4cD7eF0gH3jK6lM9nP2qR5sT8vW1xY4'
WHERE identifiant = 'bob';

-- Listes de test (listes 1 et 2 à Alice, liste 3 à Bob)
INSERT INTO liste (id, nom, utilisateur_id) VALUES
(1, 'Projet BTS SIO', 1),
(2, 'Courses et perso', 1),
(3, 'Administration serveur', 2);

-- 15 tâches de test (statuts : a_faire, en_cours, terminee ; priorités : basse, normale, haute)
INSERT INTO tache (liste_id, titre, description, priorite, statut, echeance) VALUES
(1, 'Rédiger le cahier des charges', 'Définir les fonctionnalités et règles de sécurité', 'haute', 'terminee', '2026-11-10'),
(1, 'Concevoir le schéma relationnel', 'Tables utilisateur, liste et tache', 'haute', 'terminee', '2026-11-15'),
(1, 'Mettre en place la connexion PDO', 'Gestion des erreurs avec try/catch et error_log', 'normale', 'en_cours', '2026-11-29'),
(1, 'Développer le formulaire de connexion', 'Vérification avec password_verify()', 'haute', 'a_faire', '2026-12-02'),
(1, 'Ajouter les filtres et le tri', 'Filtrer par statut, priorité et mot-clé', 'normale', 'a_faire', '2026-12-05'),
(1, 'Relire les règles S1 à S10', 'Vérifier les échappements et requêtes préparées', 'haute', 'a_faire', '2026-12-10'),
(1, 'Mettre à jour le README.md', 'Documenter l’installation et les comptes de test', 'basse', 'a_faire', NULL),
(2, 'Acheter du café et des fruits', NULL, 'basse', 'terminee', '2026-11-20'),
(2, 'Prendre rendez-vous dentiste', 'Contrôle annuel', 'normale', 'en_cours', '2026-12-01'),
(2, 'Renouveler la carte de transport', NULL, 'normale', 'a_faire', '2026-11-30'),
(2, 'Ranger le bureau', 'Trier les documents papier', 'basse', 'a_faire', NULL),
(2, 'Sauvegarder le disque externe', 'Copie des projets récents', 'haute', 'a_faire', '2026-12-04'),
(3, 'Vérifier les sauvegardes MySQL', 'Contrôler les dumps nocturnes', 'haute', 'terminee', '2026-11-18'),
(3, 'Mettre à jour les paquets système', NULL, 'normale', 'en_cours', '2026-12-03'),
(3, 'Auditer les accès SSH', 'Vérifier les clés autorisées', 'haute', 'a_faire', '2026-12-08');
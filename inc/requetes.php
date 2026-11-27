<?php
declare(strict_types=1);

/**
 * Cherche un utilisateur par son identifiant.
 * Renvoie un tableau associatif si trouvé, ou null sinon.
 *
 * @return array{id: int, identifiant: string, mdp_hash: string, date_inscription: string}|null
 */
function trouverUtilisateurParIdentifiant(PDO $pdo, string $identifiant): ?array
{
    $sql = "SELECT id, identifiant, mdp_hash, date_inscription
            FROM utilisateur
            WHERE identifiant = :identifiant
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":identifiant" => $identifiant,
    ]);

    $utilisateur = $stmt->fetch();
    return $utilisateur === false ? null : $utilisateur;
}

/**
 * Ajoute un nouvel utilisateur en base avec un mot de passe déjà haché.
 * Renvoie l'identifiant (id) créé.
 */
function creerUtilisateur(PDO $pdo, string $identifiant, string $mdpHash): int
{
    $sql = "INSERT INTO utilisateur (identifiant, mdp_hash)
            VALUES (:identifiant, :mdp_hash)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":identifiant" => $identifiant,
        ":mdp_hash"    => $mdpHash,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Récupère les listes d'un utilisateur avec :
 * - le nombre total de tâches
 * - le nombre de tâches terminées
 *
 * @return array<int, array{
 *     id: int,
 *     nom: string,
 *     utilisateur_id: int,
 *     date_creation: string,
 *     nb_taches: int|string,
 *     nb_terminees: int|string
 * }>
 */
function recupererListesUtilisateur(PDO $pdo, int $utilisateurId): array
{
    $sql = "SELECT
                l.id,
                l.nom,
                l.utilisateur_id,
                l.date_creation,
                COUNT(t.id) AS nb_taches,
                COALESCE(SUM(CASE WHEN t.statut = 'terminee' THEN 1 ELSE 0 END), 0) AS nb_terminees
            FROM liste l
            LEFT JOIN tache t ON t.liste_id = l.id
            WHERE l.utilisateur_id = :utilisateur_id
            GROUP BY l.id, l.nom, l.utilisateur_id, l.date_creation
            ORDER BY l.date_creation DESC, l.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":utilisateur_id" => $utilisateurId,
    ]);

    return $stmt->fetchAll();
}

/**
 * Récupère une liste uniquement si elle existe ET appartient à l'utilisateur indiqué.
 * Renvoie null sinon (Règle S6).
 *
 * @return array{id: int, nom: string, utilisateur_id: int, date_creation: string}|null
 */
function trouverListePourUtilisateur(PDO $pdo, int $listeId, int $utilisateurId): ?array
{
    $sql = "SELECT id, nom, utilisateur_id, date_creation
            FROM liste
            WHERE id = :id
              AND utilisateur_id = :utilisateur_id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id"             => $listeId,
        ":utilisateur_id" => $utilisateurId,
    ]);

    $liste = $stmt->fetch();
    return $liste === false ? null : $liste;
}

/**
 * Récupère toutes les tâches d'une liste donnée.
 *
 * @return array<int, array{
 *     id: int,
 *     liste_id: int,
 *     titre: string,
 *     description: string|null,
 *     priorite: string,
 *     statut: string,
 *     echeance: string|null,
 *     date_creation: string
 * }>
 */
function recupererTachesDeListe(PDO $pdo, int $listeId): array
{
    $sql = "SELECT
                id,
                liste_id,
                titre,
                description,
                priorite,
                statut,
                echeance,
                date_creation
            FROM tache
            WHERE liste_id = :liste_id
            ORDER BY id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":liste_id" => $listeId,
    ]);

    return $stmt->fetchAll();
}

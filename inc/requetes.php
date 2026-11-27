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
/**
 * Crée une liste pour l'utilisateur connecté et renvoie son identifiant.
 */
function creerListe(PDO $pdo, int $utilisateurId, string $nom): int
{
    $sql = "INSERT INTO liste (nom, utilisateur_id)
            VALUES (:nom, :utilisateur_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nom"            => $nom,
        ":utilisateur_id" => $utilisateurId,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Renomme une liste uniquement si elle appartient à l'utilisateur connecté.
 * Renvoie true si une ligne a été modifiée, false sinon.
 */
function renommerListe(PDO $pdo, int $listeId, int $utilisateurId, string $nom): bool
{
    $sql = "UPDATE liste
            SET nom = :nom
            WHERE id = :id
              AND utilisateur_id = :utilisateur_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nom"            => $nom,
        ":id"             => $listeId,
        ":utilisateur_id" => $utilisateurId,
    ]);

    return $stmt->rowCount() > 0;
}

/**
 * Supprime une liste uniquement si elle appartient à l'utilisateur connecté.
 * Les tâches liées sont supprimées automatiquement par ON DELETE CASCADE.
 * Renvoie true si une ligne a été supprimée, false sinon.
 */
function supprimerListe(PDO $pdo, int $listeId, int $utilisateurId): bool
{
    $sql = "DELETE FROM liste
            WHERE id = :id
              AND utilisateur_id = :utilisateur_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id"             => $listeId,
        ":utilisateur_id" => $utilisateurId,
    ]);

    return $stmt->rowCount() > 0;
}

/**
 * Récupère une tâche uniquement si elle appartient à une liste détenue par l'utilisateur connecté.
 * Renvoie un tableau associatif ou null si introuvable / non autorisée.
 *
 * @return array{
 *     id: int,
 *     liste_id: int,
 *     titre: string,
 *     description: string|null,
 *     priorite: string,
 *     statut: string,
 *     echeance: string|null,
 *     date_creation: string
 * }|null
 */
function trouverTachePourUtilisateur(PDO $pdo, int $tacheId, int $utilisateurId): ?array
{
    $sql = "SELECT
                t.id,
                t.liste_id,
                t.titre,
                t.description,
                t.priorite,
                t.statut,
                t.echeance,
                t.date_creation
            FROM tache t
            INNER JOIN liste l ON l.id = t.liste_id
            WHERE t.id = :tache_id
              AND l.utilisateur_id = :utilisateur_id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":tache_id"       => $tacheId,
        ":utilisateur_id" => $utilisateurId,
    ]);

    $tache = $stmt->fetch();
    return $tache === false ? null : $tache;
}

/**
 * Ajoute une tâche dans une liste et renvoie son identifiant.
 */
function ajouterTache(
    PDO $pdo,
    int $listeId,
    string $titre,
    ?string $description,
    string $priorite,
    string $statut,
    ?string $echeance
): int {
    $sql = "INSERT INTO tache (liste_id, titre, description, priorite, statut, echeance)
            VALUES (:liste_id, :titre, :description, :priorite, :statut, :echeance)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":liste_id"    => $listeId,
        ":titre"       => $titre,
        ":description" => $description,
        ":priorite"    => $priorite,
        ":statut"      => $statut,
        ":echeance"    => $echeance,
    ]);

    return (int) $pdo->lastInsertId();
}

/**
 * Met à jour le statut d'une tâche uniquement si sa liste appartient à l'utilisateur connecté.
 */
function changerStatutTache(PDO $pdo, int $tacheId, int $utilisateurId, string $statut): bool
{
    $sql = "UPDATE tache t
            INNER JOIN liste l ON l.id = t.liste_id
            SET t.statut = :statut
            WHERE t.id = :tache_id
              AND l.utilisateur_id = :utilisateur_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":statut"         => $statut,
        ":tache_id"       => $tacheId,
        ":utilisateur_id" => $utilisateurId,
    ]);

    return $stmt->rowCount() > 0;
}

/**
 * Supprime une tâche uniquement si sa liste appartient à l'utilisateur connecté.
 */
function supprimerTache(PDO $pdo, int $tacheId, int $utilisateurId): bool
{
    $sql = "DELETE t
            FROM tache t
            INNER JOIN liste l ON l.id = t.liste_id
            WHERE t.id = :tache_id
              AND l.utilisateur_id = :utilisateur_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":tache_id"       => $tacheId,
        ":utilisateur_id" => $utilisateurId,
    ]);

    return $stmt->rowCount() > 0;
}
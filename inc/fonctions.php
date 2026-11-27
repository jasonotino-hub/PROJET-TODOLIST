<?php
declare(strict_types=1);

/**
 * Échappe une chaîne de caractères pour un affichage HTML sécurisé (Règle S2).
 */
function e(?string $texte): string
{
    return htmlspecialchars($texte ?? "", ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

/**
 * Indique si un utilisateur est actuellement connecté.
 */
function estConnecte(): bool
{
    return isset($_SESSION["utilisateur_id"], $_SESSION["utilisateur_identifiant"]);
}

/**
 * Renvoie l'identifiant de l'utilisateur connecté, ou null s'il ne l'est pas.
 */
function utilisateurConnecteId(): ?int
{
    if (!estConnecte()) {
        return null;
    }
    return (int) $_SESSION["utilisateur_id"];
}

/**
 * Renvoie le nom/identifiant de l'utilisateur connecté, ou null.
 */
function utilisateurConnecteNom(): ?string
{
    if (!estConnecte()) {
        return null;
    }
    return (string) $_SESSION["utilisateur_identifiant"];
}

/**
 * Enregistre un message flash (type : "succes", "erreur", "info") en session.
 */
function ajouterMessage(string $type, string $texte): void
{
    $_SESSION["messages"][] = [
        "type"  => $type,
        "texte" => $texte,
    ];
}

/**
 * Récupère puis supprime tous les messages flash de la session.
 *
 * @return array<int, array{type: string, texte: string}>
 */
function consommerMessages(): array
{
    $messages = $_SESSION["messages"] ?? [];
    unset($_SESSION["messages"]);
    return $messages;
}

/**
 * Redirige vers une URL et arrête immédiatement l'exécution du script.
 */
function rediriger(string $chemin): never
{
    header("Location: " . $chemin);
    exit;
}

/**
 * Réserve la page courante aux utilisateurs connectés (Règle S5).
 * Redirige vers connexion.php avec un message si le visiteur n'est pas connecté.
 */
function exigerConnexion(): void
{
    if (!estConnecte()) {
        ajouterMessage("erreur", "Vous devez être connecté pour accéder à cette page.");
        rediriger("connexion.php");
    }
}

/**
 * Libellés autorisés pour le statut d'une tâche.
 *
 * @return array<string, string>
 */
function libellesStatuts(): array
{
    return [
        "a_faire"  => "À faire",
        "en_cours" => "En cours",
        "terminee" => "Terminée",
    ];
}

/**
 * Libellés autorisés pour la priorité d'une tâche.
 *
 * @return array<string, string>
 */
function libellesPriorites(): array
{
    return [
        "basse"   => "Basse",
        "normale" => "Normale",
        "haute"   => "Haute",
    ];
}

/**
 * Renvoie le libellé d'un statut, ou "Inconnu" si non reconnu.
 */
function libelleStatut(string $statut): string
{
    return libellesStatuts()[$statut] ?? "Inconnu";
}

/**
 * Renvoie le libellé d'une priorité, ou "Inconnue" si non reconnue.
 */
function libellePriorite(string $priorite): string
{
    return libellesPriorites()[$priorite] ?? "Inconnue";
}

/**
 * Renvoie la classe CSS du badge associé à un statut.
 */
function classeStatut(string $statut): string
{
    return match ($statut) {
        "terminee" => "badge badge-vert",
        "en_cours" => "badge badge-bleu",
        default    => "badge badge-gris",
    };
}

/**
 * Renvoie la classe CSS du badge associé à une priorité.
 */
function classePriorite(string $priorite): string
{
    return match ($priorite) {
        "haute"   => "badge badge-rouge",
        "normale" => "badge badge-ambre",
        default   => "badge badge-gris",
    };
}
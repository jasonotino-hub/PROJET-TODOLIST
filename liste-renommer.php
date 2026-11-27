<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";
exigerConnexion();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    rediriger("mes-listes.php");
}

$utilisateurId = utilisateurConnecteId();
$listeId = (int) ($_POST["liste_id"] ?? 0);
$nom = trim($_POST["nom"] ?? "");

if ($listeId <= 0 || trouverListePourUtilisateur($pdo, $listeId, $utilisateurId) === null) {
    $message404 = "Cette liste est introuvable ou ne vous appartient pas.";
    require __DIR__ . "/inc/404.php";
}

if ($nom === "") {
    ajouterMessage("erreur", "Le nouveau nom de la liste est obligatoire.");
    rediriger("liste.php?id=" . $listeId);
}

if (mb_strlen($nom) > 100) {
    ajouterMessage("erreur", "Le nom de la liste ne doit pas dépasser 100 caractères.");
    rediriger("liste.php?id=" . $listeId);
}

renommerListe($pdo, $listeId, $utilisateurId, $nom);
ajouterMessage("succes", "La liste a bien été renommée en « " . $nom . " ».");
rediriger("liste.php?id=" . $listeId);
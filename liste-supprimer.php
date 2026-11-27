<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";
exigerConnexion();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    rediriger("mes-listes.php");
}

$utilisateurId = utilisateurConnecteId();
$listeId = (int) ($_POST["liste_id"] ?? 0);

if ($listeId <= 0 || trouverListePourUtilisateur($pdo, $listeId, $utilisateurId) === null) {
    $message404 = "Cette liste est introuvable ou ne vous appartient pas.";
    require __DIR__ . "/inc/404.php";
}

supprimerListe($pdo, $listeId, $utilisateurId);
ajouterMessage("succes", "La liste et ses tâches ont bien été supprimées.");
rediriger("mes-listes.php");
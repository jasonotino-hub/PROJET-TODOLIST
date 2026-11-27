<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";
exigerConnexion();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    rediriger("mes-listes.php");
}

$utilisateurId = utilisateurConnecteId();
$nom = trim($_POST["nom"] ?? "");

if ($nom === "") {
    ajouterMessage("erreur", "Le nom de la liste est obligatoire.");
    rediriger("mes-listes.php");
}

if (mb_strlen($nom) > 100) {
    ajouterMessage("erreur", "Le nom de la liste ne doit pas dépasser 100 caractères.");
    rediriger("mes-listes.php");
}

creerListe($pdo, $utilisateurId, $nom);
ajouterMessage("succes", "La liste « " . $nom . " » a bien été créée.");
rediriger("mes-listes.php");
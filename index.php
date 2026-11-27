<?php
// Avant d'inclure ce fichier, la page définit $titrePage et $pageActive.
$titrePage = $titrePage ?? "Accueil";
$pageActive = $pageActive ?? "index.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titrePage) ?> — ToDo List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="entete">
    <a href="index.php" class="logo">ToDo<span>List</span></a>
    <nav class="menu">
        <a href="index.php" class="<?= $pageActive === "index.php" ? "actif" : "" ?>">Accueil</a>
    </nav>
</header>
<main class="contenu">
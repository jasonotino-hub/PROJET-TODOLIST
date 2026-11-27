<?php
$titrePage = $titrePage ?? "Accueil";
$pageActive = $pageActive ?? "index.php";
$messagesFlash = consommerMessages();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titrePage) ?> — ToDo List</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="entete">
    <a href="index.php" class="logo">ToDo<span>List</span></a>
    <nav class="menu">
        <a href="index.php" class="<?= $pageActive === "index.php" ? "actif" : "" ?>">Accueil</a>
        <?php if (estConnecte()): ?>
            <span class="utilisateur-badge"><?= e(utilisateurConnecteNom()) ?></span>
            <form method="post" action="deconnexion.php" class="form-inline">
                <button type="submit" class="bouton bouton-leger">Déconnexion</button>
            </form>
        <?php else: ?>
            <a href="connexion.php" class="<?= $pageActive === "connexion.php" ? "actif" : "" ?>">Connexion</a>
            <a href="inscription.php" class="<?= $pageActive === "inscription.php" ? "actif" : "" ?>">Inscription</a>
        <?php endif; ?>
    </nav>
</header>
<main class="contenu">
    <?php foreach ($messagesFlash as $msg): ?>
        <div class="alerte alerte-<?= e($msg["type"]) ?>">
            <?= e($msg["texte"]) ?>
        </div>
    <?php endforeach; ?>
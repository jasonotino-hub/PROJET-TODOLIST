<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";

$titrePage = "Accueil";
$pageActive = "index.php";

require __DIR__ . "/inc/entete.php";
?>

<h1>Bienvenue sur ToDo List</h1>
<p class="intro">Application de gestion de tâches personnelles.</p>

<?php require __DIR__ . "/inc/pied.php"; ?>
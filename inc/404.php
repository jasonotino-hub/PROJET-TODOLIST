<?php
// À inclure après config/init.php lorsqu'une ressource est introuvable ou interdite d'accès.
http_response_code(404);
$titrePage = "Page introuvable";
$pageActive = $pageActive ?? "";

require __DIR__ . "/entete.php";
?>

<h1>Page introuvable</h1>
<p class="vide">
    <?= isset($message404) ? e($message404) : "La ressource demandée n'existe pas ou n'est pas accessible." ?>
</p>
<p>
    <a href="mes-listes.php" class="bouton">Retour à mes listes</a>
</p>

<?php
require __DIR__ . "/pied.php";
exit;
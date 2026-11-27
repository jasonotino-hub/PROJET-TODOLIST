<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";
exigerConnexion();

$pageActive = "mes-listes.php";
$utilisateurId = utilisateurConnecteId();
$listeId = (int) ($_GET["id"] ?? 0);

$liste = $listeId > 0 ? trouverListePourUtilisateur($pdo, $listeId, $utilisateurId) : null;

if ($liste === null) {
    $message404 = "Cette liste est introuvable ou ne vous appartient pas.";
    require __DIR__ . "/inc/404.php";
}

$taches = recupererTachesDeListe($pdo, (int) $liste["id"]);
$titrePage = "Liste : " . $liste["nom"];

require __DIR__ . "/inc/entete.php";
?>

<p class="fil">
    <a href="mes-listes.php">&larr; Retour à mes listes</a>
</p>

<h1><?= e($liste["nom"]) ?></h1>

<?php if (count($taches) === 0): ?>
    <p class="vide">Aucune tâche dans cette liste pour le moment.</p>
<?php else: ?>
    <table class="tableau">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Échéance</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($taches as $tache): ?>
                <tr>
                    <td>
                        <strong><?= e($tache["titre"]) ?></strong>
                        <?php if ($tache["description"] !== null && $tache["description"] !== ""): ?>
                            <p class="description-tache"><?= e($tache["description"]) ?></p>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="<?= classePriorite((string) $tache["priorite"]) ?>">
                            <?= e(libellePriorite((string) $tache["priorite"])) ?>
                        </span>
                    </td>
                    <td>
                        <span class="<?= classeStatut((string) $tache["statut"]) ?>">
                            <?= e(libelleStatut((string) $tache["statut"])) ?>
                        </span>
                    </td>
                    <td><?= $tache["echeance"] !== null ? e($tache["echeance"]) : "—" ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require __DIR__ . "/inc/pied.php"; ?>
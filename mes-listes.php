<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";
exigerConnexion();

$titrePage = "Mes listes";
$pageActive = "mes-listes.php";

$utilisateurId = utilisateurConnecteId();
$listes = recupererListesUtilisateur($pdo, $utilisateurId);

// Préparation des données d'affichage (pourcentage arrondi)
$listesAffichage = [];
foreach ($listes as $liste) {
    $nbTaches = (int) $liste["nb_taches"];
    $nbTerminees = (int) $liste["nb_terminees"];
    $pourcentage = $nbTaches > 0 ? (int) round(($nbTerminees / $nbTaches) * 100) : 0;

    $listesAffichage[] = [
        "id"           => (int) $liste["id"],
        "nom"          => (string) $liste["nom"],
        "nb_taches"    => $nbTaches,
        "nb_terminees" => $nbTerminees,
        "pourcentage"  => $pourcentage,
    ];
}

require __DIR__ . "/inc/entete.php";
?>

<h1>Mes listes</h1>

<?php if (count($listesAffichage) === 0): ?>
    <p class="vide">Vous n'avez pas encore de liste.</p>
<?php else: ?>
    <div class="grille-listes">
        <?php foreach ($listesAffichage as $liste): ?>
            <article class="carte-liste">
                <h2>
                    <a href="liste.php?id=<?= $liste["id"] ?>">
                        <?= e($liste["nom"]) ?>
                    </a>
                </h2>
                <p class="meta">
                    <?= $liste["nb_taches"] ?> tâche(s) — <?= $liste["nb_terminees"] ?> terminée(s)
                </p>
                <p class="progression">
                    Avancement : <strong><?= $liste["pourcentage"] ?> %</strong>
                </p>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . "/inc/pied.php"; ?>
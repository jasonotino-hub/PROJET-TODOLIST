<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";
exigerConnexion();

$titrePage = "Mes listes";
$pageActive = "mes-listes.php";

$utilisateurId = utilisateurConnecteId();
$listes = recupererListesUtilisateur($pdo, $utilisateurId);

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

<form method="post" action="liste-creer.php" class="formulaire formulaire-ligne">
    <label class="champ-croissant">
        Nouvelle liste
        <input type="text" name="nom" placeholder="Ex. Révisions, Courses..." maxlength="100" required>
    </label>
    <button type="submit" class="bouton">Créer la liste</button>
</form>

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
                <div class="carte-actions">
                    <a href="liste.php?id=<?= $liste["id"] ?>" class="bouton bouton-secondaire bouton-petit">Ouvrir</a>
                    <form method="post" action="liste-supprimer.php" class="form-inline"
                          onsubmit="return confirm('Supprimer cette liste et toutes ses tâches ?');">
                        <input type="hidden" name="liste_id" value="<?= $liste["id"] ?>">
                        <button type="submit" class="bouton bouton-danger bouton-petit">Supprimer</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . "/inc/pied.php"; ?>
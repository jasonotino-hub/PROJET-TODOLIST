<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";

if (estConnecte()) {
    rediriger("index.php");
}

$titrePage = "Inscription";
$pageActive = "inscription.php";

$identifiant = "";
$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifiant = trim($_POST["identifiant"] ?? "");
    $motDePasse = $_POST["mot_de_passe"] ?? "";
    $confirmation = $_POST["confirmation"] ?? "";

    if ($identifiant === "") {
        $erreurs[] = "L'identifiant est obligatoire.";
    } elseif (mb_strlen($identifiant) < 3 || mb_strlen($identifiant) > 50) {
        $erreurs[] = "L'identifiant doit contenir entre 3 et 50 caractères.";
    } elseif (trouverUtilisateurParIdentifiant($pdo, $identifiant) !== null) {
        $erreurs[] = "Cet identifiant est déjà utilisé.";
    }

    if (strlen($motDePasse) < 8) {
        $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($motDePasse !== $confirmation) {
        $erreurs[] = "La confirmation du mot de passe ne correspond pas.";
    }

    if (count($erreurs) === 0) {
        $mdpHash = password_hash($motDePasse, PASSWORD_DEFAULT);
        creerUtilisateur($pdo, $identifiant, $mdpHash);

        ajouterMessage("succes", "Votre compte a bien été créé. Vous pouvez maintenant vous connecter.");
        rediriger("connexion.php");
    }
}

require __DIR__ . "/inc/entete.php";
?>

<h1>Inscription</h1>

<?php if (count($erreurs) > 0): ?>
    <div class="alerte alerte-erreur">
        <ul>
            <?php foreach ($erreurs as $erreur): ?>
                <li><?= e($erreur) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="formulaire">
    <label>
        Identifiant
        <input type="text" name="identifiant" value="<?= e($identifiant) ?>" required>
    </label>

    <label>
        Mot de passe (8 caractères minimum)
        <input type="password" name="mot_de_passe" required>
    </label>

    <label>
        Confirmer le mot de passe
        <input type="password" name="confirmation" required>
    </label>

    <button type="submit" class="bouton">Créer mon compte</button>
</form>

<p class="lien-aide">Déjà inscrit ? <a href="connexion.php">Se connecter</a>.</p>

<?php require __DIR__ . "/inc/pied.php"; ?>
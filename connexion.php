<?php
declare(strict_types=1);

require_once __DIR__ . "/config/init.php";

if (estConnecte()) {
    rediriger("index.php");
}

$titrePage = "Connexion";
$pageActive = "connexion.php";

$identifiant = "";
$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifiant = trim($_POST["identifiant"] ?? "");
    $motDePasse = $_POST["mot_de_passe"] ?? "";

    if ($identifiant === "" || $motDePasse === "") {
        $erreurs[] = "Veuillez saisir votre identifiant et votre mot de passe.";
    } else {
        $utilisateur = trouverUtilisateurParIdentifiant($pdo, $identifiant);

        if ($utilisateur === null || !password_verify($motDePasse, $utilisateur["mdp_hash"])) {
            $erreurs[] = "Identifiant ou mot de passe incorrect.";
        } else {
            session_regenerate_id(true);
            $_SESSION["utilisateur_id"] = (int) $utilisateur["id"];
            $_SESSION["utilisateur_identifiant"] = (string) $utilisateur["identifiant"];

            ajouterMessage("succes", "Bienvenue, " . $utilisateur["identifiant"] . " !");
            rediriger("index.php");
        }
    }
}

require __DIR__ . "/inc/entete.php";
?>

<h1>Connexion</h1>

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
        Mot de passe
        <input type="password" name="mot_de_passe" required>
    </label>

    <button type="submit" class="bouton">Se connecter</button>
</form>

<p class="lien-aide">Pas encore de compte ? <a href="inscription.php">S'inscrire</a>.</p>

<?php require __DIR__ . "/inc/pied.php"; ?>
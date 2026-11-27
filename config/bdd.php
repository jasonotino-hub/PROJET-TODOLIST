<?php
declare(strict_types=1);

$dsn = "mysql:host=localhost;dbname=todolist_app;charset=utf8mb4";
$utilisateurBdd = "root";
$motDePasseBdd = "";

try {
    $pdo = new PDO($dsn, $utilisateurBdd, $motDePasseBdd, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log("Erreur connexion BDD : " . $e->getMessage());
    http_response_code(500);
    exit("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}
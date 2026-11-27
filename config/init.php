<?php
declare(strict_types=1);

ini_set("display_errors", "0");
ini_set("display_startup_errors", "0");
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../inc/fonctions.php";
require_once __DIR__ . "/../inc/requetes.php";
require_once __DIR__ . "/bdd.php";
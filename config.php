<?php
// -------------------------------------------------------------
// CONFIG.PHP
// Connexion PDO à la base MySQL "lavache"
// -------------------------------------------------------------

$host = 'localhost';
$dbname = 'lavache';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs propres
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // fetch en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                   // sécurité SQL
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Erreur de connexion MySQL : ' . $e->getMessage()
    ]));
}
?>

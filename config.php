<?php
// =============================================
// Fichier : config.php
// Connexion à la base de données Lavache
// =============================================

// Paramètres de connexion
$host     = 'localhost';
$dbname   = 'Lavache';
$username = 'root';        // Change si besoin
$password = '';            // Mets ton mot de passe MySQL s'il y en a un

try {
    // Création de la connexion PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]
    );

} catch (PDOException $e) {
    // En cas d'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
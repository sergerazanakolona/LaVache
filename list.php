<?php
// -------------------------------------------------------------
// LIST.PHP
// Récupère l'intégralité des réservations pour le Dashboard (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Inclusion de la connexion PDO centralisée, moderne et sécurisée
require 'config.php';

try {
    // Requête pour récupérer TOUTES les colonnes nécessaires, y compris l'ID crucial
    $stmt = $pdo->query("
        SELECT
            id,
            hotel_nom,
            hotel_cat,
            hotel_chbcat,
            hotel_prix,
            adresse,
            tel,
            email,
            hotel_url
        FROM reservations
        ORDER BY id DESC
    ");

    // Extraction de TOUTES les lignes d'un coup (fetchAll)
    $reservations = $stmt->fetchAll();

    // Renvoi des données sous le format attendu par la PWA et le Dashboard
    echo json_encode([
        "status" => "ok",
        "data"   => $reservations // C'est ici que se trouve le tableau exploité par le .forEach() du JS
    ]);

} catch (Exception $e) {
    // Réponse d'erreur standardisée et sécurisée
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Impossible de charger la liste des réservations.",
        "debug"   => IS_LOCAL ? $e->getMessage() : null // S'affiche uniquement en développement local
    ]);
}
?>

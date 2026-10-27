<?php
// -------------------------------------------------------------
// LIST.PHP
// Renvoie toutes les réservations au format JSON
// -------------------------------------------------------------

header('Content-Type: application/json');

// Connexion MySQL
require 'config.php';

try {
    // Requête SQL : récupérer toutes les réservations
    $stmt = $pdo->query("
        SELECT id, hotel_nom, hotel_cat, hotel_chb, hotel_prix,
               adresse, tel, email, nom_complet, created_at
        FROM reservations
        ORDER BY id DESC
    ");

    $reservations = $stmt->fetchAll();

    echo json_encode([
        'status' => 'ok',
        'count' => count($reservations),
        'data' => $reservations
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur SQL : ' . $e->getMessage()
    ]);
}
?>

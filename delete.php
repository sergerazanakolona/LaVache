<?php
// -------------------------------------------------------------
// DELETE.PHP
// Supprime une réservation via son ID (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Connexion MySQL
require 'config.php';

// Lire le JSON envoyé
$input = json_decode(file_get_contents('php://input'), true);

// Vérification
if (!$input || !isset($input['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID manquant'
    ]);
    exit;
}

$id = intval($input['id']); // sécurité

// Préparation de la requête SQL
$stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");

// Exécution
$stmt->execute([':id' => $id]);

// Vérifier si une ligne a été supprimée
if ($stmt->rowCount() > 0) {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Réservation supprimée'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Aucune réservation trouvée avec cet ID'
    ]);
}
?>

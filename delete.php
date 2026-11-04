<?php
// -------------------------------------------------------------
// DELETE.PHP
// Supprime une réservation via son ID (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Connexion MySQL centralisée
require 'config.php';

try {
    // Lire le JSON envoyé depuis l'interface
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);

    // Vérification de la présence des données
    if (!$input || !isset($input['id'])) {
        http_response_code(400); // 400 Bad Request : Requête mal formée
        echo json_encode([
            'status'  => 'error',
            'message' => 'Données invalides ou ID de réservation manquant.'
        ]);
        exit;
    }

    $id = intval($input['id']); // Sécurisation du type de l'ID

    // Préparation de la requête SQL de suppression
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :id");

    // Exécution
    $stmt->execute([':id' => $id]);

    // Vérifier si une ligne a effectivement été supprimée de la table
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'status'  => 'ok',
            'message' => 'La réservation a été supprimée avec succès.'
        ]);
    } else {
        http_response_code(404); // 404 Not Found : La ressource n'existe pas ou plus
        echo json_encode([
            'status'  => 'error',
            'message' => 'Aucune réservation trouvée en base de données avec cet ID.'
        ]);
    }

} catch (PDOException $e) {
    // En cas de panne matérielle ou de problème SQL sous XAMPP
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        'status'  => 'error',
        'message' => 'Une erreur interne est survenue lors de la suppression.',
        'debug'   => IS_LOCAL ? $e->getMessage() : null // Message système réservé au mode local
    ]);
}
?>

<?php
// -------------------------------------------------------------
// LIST.PHP
// Récupère les réservations pour le Dashboard (JSON)
// -------------------------------------------------------------

declare(strict_types=1);

// Headers de sécurité et de type
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Inclusion de la connexion PDO centralisée et sécurisée
require_once __DIR__ . '/config.php';

// Vérification minimale de la configuration
if (!isset($pdo) || !($pdo instanceof PDO)) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Erreur de configuration du serveur.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// -------------------------------------------------------------
// ⚠️ SÉCURITÉ : ajoute ici ta vérification d’authentification
// Exemple simple (à remplacer par ton vrai système) :
// if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     http_response_code(401);
//     echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
//     exit;
// }
// -------------------------------------------------------------

try {
    // Récupération de l'intégralité des lignes (adapté au fonctionnement du Dashboard actuel)
    $sql = "
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
    ";

    $stmt = $pdo->query($sql);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Envoi des données sous le format attendu par le Dashboard de votre PWA
    echo json_encode([
        'status' => 'ok',
        'data'   => $reservations
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    $response = [
        'status'  => 'error',
        'message' => 'Impossible de charger la liste des réservations.'
    ];

    // Affiche le détail uniquement en local (XAMPP) grâce à config.php
    if (defined('IS_LOCAL') && IS_LOCAL === true) {
        $response['debug'] = $e->getMessage();
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Une erreur inattendue est survenue.'
    ], JSON_UNESCAPED_UNICODE);
}
// Suppression de la balise de fermeture ?> par sécurité (bonne pratique PHP pour éviter les espaces blancs)

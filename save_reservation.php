<?php
// -------------------------------------------------------------
// RESERVE.PHP
// Insère une nouvelle offre d'hôtel / réservation (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Réutilisation de votre fichier de connexion centralisé (comme dans update.php)
require 'config.php';

try {
    // Lire le flux JSON envoyé par l'application (le clic sur "Publier")
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);

    // Vérification de la validité du JSON
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Flux de données JSON invalide ou vide'
        ]);
        exit;
    }

    // Récupération et nettoyage minimal des champs envoyés par votre JS (data)
    $hotel_nom    = trim($input['hotel_nom']    ?? '');
    $hotel_cat    = trim($input['hotel_cat']    ?? '');
    $hotel_chbcat = trim($input['hotel_chbcat']  ?? '');
    $hotel_prix   = trim($input['hotel_prix']   ?? '');
    $adresse      = trim($input['adresse']      ?? '');
    $tel          = trim($input['tel']          ?? '');
    $email        = trim($input['email']        ?? '');
    $hotel_url    = trim($input['hotel_url']    ?? '');

    // Validation minimale : Le nom de l'hôtel est obligatoire pour publier
    if (empty($hotel_nom)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Le nom de l\'hôtel est obligatoire pour créer une publication.'
        ]);
        exit;
    }

    // Préparation de la requête d'insertion (Requête préparée contre les injections SQL)
    $stmt = $pdo->prepare('
        INSERT INTO reservations 
        (hotel_nom, hotel_cat, hotel_chbcat, hotel_prix, adresse, tel, email, hotel_url) 
        VALUES (:hotel_nom, :hotel_cat, :hotel_chbcat, :hotel_prix, :adresse, :tel, :email, :hotel_url)
    ');

    // Exécution de la requête avec liaison sécurisée des variables
    $stmt->execute([
        ':hotel_nom'    => $hotel_nom,
        ':hotel_cat'    => $hotel_cat,
        ':hotel_chbcat' => $hotel_chbcat,
        ':hotel_prix'   => $hotel_prix,
        ':adresse'      => $adresse,
        ':tel'          => $tel,
        ':email'        => $email,
        ':hotel_url'    => $hotel_url
    ]);

    // Récupération de l'ID généré par MySQL pour cette insertion
    $nouvel_id = $pdo->lastInsertId();

    // Réponse de succès renvoyée à la PWA JavaScript
    echo json_encode([
        'status' => 'ok',
        'message' => 'L\'offre d\'hôtel a bien été publiée !',
        'id' => $nouvel_id
    ]);

} catch (PDOException $e) {
    // En cas de panne ou d'erreur sur votre serveur XAMPP MySQL
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur critique lors de l\'enregistrement en base de données',
        'debug' => $e->getMessage() // À masquer en production, très utile pour débugger sous VS Code
    ]);
}
?>

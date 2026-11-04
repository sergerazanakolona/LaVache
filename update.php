<?php
// -------------------------------------------------------------
// UPDATE.PHP
// Met à jour une réservation via son ID (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Connexion MySQL
require 'config.php';

try {
    // Lire le JSON envoyé
    $json = file_get_contents('php://input');
    $input = json_decode($json, true);

    // Vérification minimale
    if (!$input || !isset($input['id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID manquant ou JSON invalide'
        ]);
        exit;
    }

    $id = intval($input['id']); // Sécurité

    // 1. VÉRIFICATION DE L'EXISTENCE DE L'ID
    // Indispensable pour ne pas confondre "ID introuvable" et "Données identiques"
    $checkStmt = $pdo->prepare("SELECT id FROM reservations WHERE id = :id");
    $checkStmt->execute([':id' => $id]);
    if (!$checkStmt->fetch()) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID de réservation introuvable en base de données'
        ]);
        exit;
    }

    // Récupération des champs (Alignés sur le formulaire de l'application)
    $hotel_nom    = $input['hotel_nom']    ?? '';
    $hotel_cat    = $input['hotel_cat']    ?? '';
    $hotel_chbcat = $input['hotel_chbcat']  ?? ''; // Corrigé : aligné sur le JS
    $hotel_prix   = $input['hotel_prix']   ?? '';
    $adresse      = $input['adresse']      ?? '';
    $tel          = $input['tel']          ?? '';
    $email        = $input['email']        ?? '';
    $hotel_url    = $input['hotel_url']    ?? ''; // Ajouté : présent dans le formulaire

    // Préparation de la requête SQL
    $stmt = $pdo->prepare("
        UPDATE reservations
        SET hotel_nom = :hotel_nom,
            hotel_cat = :hotel_cat,
            hotel_chbcat = :hotel_chbcat,
            hotel_prix = :hotel_prix,
            adresse = :adresse,
            tel = :tel,
            email = :email,
            hotel_url = :hotel_url
        WHERE id = :id
    ");

    // Exécution
    $stmt->execute([
        ':hotel_nom'    => $hotel_nom,
        ':hotel_cat'    => $hotel_cat,
        ':hotel_chbcat' => $hotel_chbcat,
        ':hotel_prix'   => $hotel_prix,
        ':adresse'      => $adresse,
        ':tel'          => $tel,
        ':email'        => $email,
        ':hotel_url'    => $hotel_url,
        ':id'           => $id
    ]);

    // Si on arrive ici, l'ID existait et la requête s'est exécutée sans plantage
    echo json_encode([
        'status' => 'ok',
        'message' => 'Réservation mise à jour avec succès'
    ]);

} catch (PDOException $e) {
    // En cas de plantage SQL (connexion perdue, table bloquée, etc.)
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de base de données lors de la mise à jour',
        'debug' => $e->getMessage() // Optionnel : à retirer en production
    ]);
}
?>

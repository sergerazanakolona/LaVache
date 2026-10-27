<?php
// -------------------------------------------------------------
// UPDATE.PHP
// Met à jour une réservation via son ID (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Connexion MySQL
require 'config.php';

// Lire le JSON envoyé
$input = json_decode(file_get_contents('php://input'), true);

// Vérification minimale
if (!$input || !isset($input['id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID manquant'
    ]);
    exit;
}

$id = intval($input['id']); // sécurité

// Récupération des champs envoyés
$hotel_nom    = $input['hotel_nom']    ?? '';
$hotel_cat    = $input['hotel_cat']    ?? '';
$hotel_chb    = $input['hotel_chb']    ?? '';
$hotel_prix   = $input['hotel_prix']   ?? '';
$adresse      = $input['adresse']      ?? '';
$tel          = $input['tel']          ?? '';
$email        = $input['email']        ?? '';
$nom_complet  = $input['nom_complet']  ?? '';

// Préparation de la requête SQL
$stmt = $pdo->prepare("
    UPDATE reservations
    SET hotel_nom = :hotel_nom,
        hotel_cat = :hotel_cat,
        hotel_chb = :hotel_chb,
        hotel_prix = :hotel_prix,
        adresse = :adresse,
        tel = :tel,
        email = :email,
        nom_complet = :nom_complet
    WHERE id = :id
");

// Exécution
$stmt->execute([
    ':hotel_nom'    => $hotel_nom,
    ':hotel_cat'    => $hotel_cat,
    ':hotel_chb'    => $hotel_chb,
    ':hotel_prix'   => $hotel_prix,
    ':adresse'      => $adresse,
    ':tel'          => $tel,
    ':email'        => $email,
    ':nom_complet'  => $nom_complet,
    ':id'           => $id
]);

// Vérifier si une ligne a été modifiée
if ($stmt->rowCount() > 0) {
    echo json_encode([
        'status' => 'ok',
        'message' => 'Réservation mise à jour'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Aucune modification ou ID introuvable'
    ]);
}
?>

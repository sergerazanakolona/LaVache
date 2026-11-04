<?php
// -------------------------------------------------------------
// RESERVE.PHP
// Insère une nouvelle offre de chambre d'hôtel en base (JSON)
// -------------------------------------------------------------

header('Content-Type: application/json');

// Inclusion de la connexion PDO centralisée, moderne et sécurisée
require 'config.php';

try {
    // Lire le flux JSON envoyé par l'application (le clic sur "Publier")
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Vérification de la validité du JSON
    if (!$data) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Aucune donnée reçue ou flux JSON invalide'
        ]);
        exit;
    }

    // Récupération sécurisée et nettoyage des valeurs (Clés alignées sur le JS de index.html)
    $hotel_nom    = trim($data['hotel_nom']    ?? '');
    $hotel_cat    = trim($data['hotel_cat']    ?? '');
    $hotel_chbcat = trim($data['hotel_chbcat']  ?? ''); // Clé synchronisée
    $hotel_prix   = trim($data['hotel_prix']   ?? '');
    $adresse      = trim($data['adresse']      ?? '');
    $tel          = trim($data['tel']          ?? '');
    $email        = trim($data['email']        ?? '');
    $hotel_url    = trim($data['hotel_url']    ?? '');

    // Validation minimale : Le nom de l'hôtel est obligatoire
    if (empty($hotel_nom)) {
        http_response_code(400);
        echo json_encode([
            'status'  => 'error',
            'message' => 'Le nom de l\'hôtel est requis pour publier une offre.'
        ]);
        exit;
    }

    // Requête SQL d'insertion
    // Correction : "hotel_chb" est remplacé par "hotel_chbcat" pour correspondre à votre table MySQL
    $sql = "
        INSERT INTO reservations
        (
            hotel_nom,
            hotel_cat,
            hotel_chbcat,
            hotel_prix,
            adresse,
            tel,
            email,
            hotel_url,
            created_at
        )
        VALUES
        (
            :hotel_nom,
            :hotel_cat,
            :hotel_chbcat,
            :hotel_prix,
            :adresse,
            :tel,
            :email,
            :hotel_url,
            NOW()
        )
    ";

    $stmt = $pdo->prepare($sql);

    // Exécution avec liaison sécurisée des paramètres
    $stmt->execute([
        ":hotel_nom"    => $hotel_nom,
        ":hotel_cat"    => $hotel_cat,
        ":hotel_chbcat" => $hotel_chbcat,
        ":hotel_prix"   => $hotel_prix,
        ":adresse"      => $adresse,
        ":tel"          => $tel,
        ":email"        => $email,
        ":hotel_url"    => $hotel_url
    ]);

    // Récupération de l'ID généré pour cette nouvelle ligne
    $nouvel_id = $pdo->lastInsertId();

    // Réponse de succès standardisée (Utilise 'status' => 'ok' pour votre JS)
    echo json_encode([
        'status'  => 'ok',
        'message' => 'L\'offre d\'hôtel a bien été publiée !',
        'id'      => $nouvel_id
    ]);

} catch (Exception $e) {
    // Aligné sur le format d'erreur standard de votre PWA
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Erreur critique lors de l'enregistrement de l'offre.",
        "debug"   => IS_LOCAL ? $e->getMessage() : null // S'affiche uniquement en local sous XAMPP
    ]);
}
?>

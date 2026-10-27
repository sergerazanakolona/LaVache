<?php
// save_reservation.php

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    exit;
}

$nom = $input['nom'] ?? '';
$email = $input['email'] ?? '';
$date_arrivee = $input['date_arrivee'] ?? '';
$date_depart = $input['date_depart'] ?? '';
$chambre = $input['chambre'] ?? '';

// TODO: sécuriser / valider

$pdo = new PDO('mysql:host=localhost;dbname=lavache;charset=utf8', 'root', '');
$stmt = $pdo->prepare('INSERT INTO reservations (nom, email, date_arrivee, date_depart, chambre) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$nom, $email, $date_arrivee, $date_depart, $chambre]);

echo json_encode(['status' => 'ok']);

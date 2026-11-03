<?php

header('Content-Type: application/json');

try {

    $pdo = new PDO(
        "mysql:host=localhost;dbname=lavache;charset=utf8",
        "root",
        ""
    );

    $stmt = $pdo->query("
        SELECT
            hotel_nom,
            hotel_cat,
            hotel_chb,
            hotel_prix,
            adresse,
            tel,
            email,
            hotel_url
        FROM reservations
        ORDER BY id DESC
        LIMIT 1
    ");

    $offer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$offer) {

        echo json_encode([
            "hotel_nom" => "",
            "hotel_cat" => "",
            "hotel_chb" => "",
            "hotel_prix" => "",
            "adresse" => "",
            "tel" => "",
            "email" => "",
            "hotel_url" => ""
        ]);

        exit;
    }

    echo json_encode($offer);

}
catch(Exception $e){

    echo json_encode([
        "error" => $e->getMessage()
    ]);

}
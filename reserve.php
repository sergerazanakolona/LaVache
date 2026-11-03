<?php

header("Content-Type: application/json");

try {

    $pdo = new PDO(
        "mysql:host=localhost;dbname=lavache;charset=utf8",
        "root",
        ""
    );

    $data = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!$data) {

        echo json_encode([
            "success" => false,
            "error" => "Aucune donnée reçue"
        ]);

        exit;
    }

    $sql = "
        INSERT INTO reservations
        (
            hotel_nom,
            hotel_cat,
            hotel_chb,
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
            :hotel_chb,
            :hotel_prix,
            :adresse,
            :tel,
            :email,
            :hotel_url,
            NOW()
        )
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ":hotel_nom" =>
            $data["hotel_nom"],

        ":hotel_cat" =>
            $data["hotel_cat"],

        ":hotel_chb" =>
            $data["hotel_chbcat"],

        ":hotel_prix" =>
            $data["hotel_prix"],

        ":adresse" =>
            $data["adresse"],

        ":tel" =>
            $data["tel"],

        ":email" =>
            $data["email"],

        ":hotel_url" =>
            $data["hotel_url"]

    ]);

    echo json_encode([
        "success" => true
    ]);

}
catch(Exception $e){

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);

}
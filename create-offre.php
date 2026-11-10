<?php
// =============================================
// create-offre.php - Version finale
// =============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

// Vérifie que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: hotelier.html');
    exit;
}

// Récupération des données
$type_chambre = trim($_POST['type_chambre'] ?? '');
$prix         = floatval($_POST['prix'] ?? 0);
$url          = trim($_POST['url'] ?? '');
$hotel_id     = 1; // Hôtel de test

// Validation
if (empty($type_chambre) || $prix <= 0) {
    die('Erreur : Type de chambre et prix sont obligatoires.');
}

try {
    // 1. Mettre à jour l'URL de l'hôtel
    if (!empty($url)) {
        $stmt = $pdo->prepare("UPDATE hotels SET url = ? WHERE id = ?");
        $stmt->execute([$url, $hotel_id]);
    }

    // 2. Insérer la nouvelle offre
    $sql = "INSERT INTO offres (hotel_id, type_chambre, prix, expire_le, statut) 
            VALUES (?, ?, ?, CURDATE(), 'active')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hotel_id, $type_chambre, $prix]);

    // Succès
    echo "<script>
            alert('Offre publiée avec succès !');
            window.location.href = 'hotelier.html';
          </script>";

} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>
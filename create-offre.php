<?php
// =============================================
// create-offre.php
// Publie une offre pour l'hôtel choisi (compte hôtelier)
// =============================================

session_start();
require_once 'config.php';

// Doit être connecté en hôtelier
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'hotelier') {
    header('Location: connexion.html');
    exit;
}

// Uniquement en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: hotelier.php');
    exit;
}

$user_id      = (int) $_SESSION['user_id'];
$hotel_id     = (int) ($_POST['hotel_id'] ?? 0);
$type_chambre = trim($_POST['type_chambre'] ?? '');
$prix         = floatval($_POST['prix'] ?? 0);
$url          = trim($_POST['url'] ?? '');

// Validation
if ($hotel_id <= 0) {
    die('Veuillez choisir un hôtel.');
}
if ($type_chambre === '' || $prix <= 0) {
    die('Le type de chambre et le prix sont obligatoires.');
}

try {
    // Vérifier que l'hôtel appartient à cet hôtelier
    $stmt = $pdo->prepare("SELECT id, nom FROM hotels WHERE id = ? AND user_id = ?");
    $stmt->execute([$hotel_id, $user_id]);
    $hotel = $stmt->fetch();

    if (!$hotel) {
        die('Hôtel non autorisé pour ce compte. Vérifiez que l\'hôtel est bien rattaché à votre utilisateur.');
    }

    // Mettre à jour l'URL de l'hôtel si fournie
    if ($url !== '') {
        $stmt = $pdo->prepare("UPDATE hotels SET url = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$url, $hotel_id, $user_id]);
    }

    // Créer l'offre
    $stmt = $pdo->prepare("
        INSERT INTO offres (hotel_id, type_chambre, prix, expire_le, statut)
        VALUES (?, ?, ?, CURDATE(), 'active')
    ");
    $stmt->execute([$hotel_id, $type_chambre, $prix]);

    echo "<script>
            alert('Offre publiée avec succès pour " . addslashes($hotel['nom']) . " !');
            window.location.href = 'hotelier.php';
          </script>";
    exit;

} catch (PDOException $e) {
    die('Erreur lors de la publication : ' . $e->getMessage());
}
?>
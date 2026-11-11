<?php
// =============================================
// reserver.php - Enregistre le clic et redirige vers l'hôtel
// =============================================

session_start();
require_once 'config.php';

$offre_id = intval($_GET['offre_id'] ?? 0);

if ($offre_id <= 0) {
    die('Offre invalide.');
}

try {
    // Récupérer l'offre + hôtel
    $stmt = $pdo->prepare("
        SELECT o.id AS offre_id, o.hotel_id, o.statut, h.url, h.nom
        FROM offres o
        JOIN hotels h ON o.hotel_id = h.id
        WHERE o.id = ? AND o.statut = 'active'
    ");
    $stmt->execute([$offre_id]);
    $offre = $stmt->fetch();

    if (!$offre) {
        die('Offre introuvable ou expirée.');
    }

    // Enregistrer le clic
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

    $stmt = $pdo->prepare("
        INSERT INTO clics_reservation (offre_id, hotel_id, user_id, ip_address, user_agent, statut)
        VALUES (?, ?, ?, ?, ?, 'clic')
    ");
    $stmt->execute([
        $offre['offre_id'],
        $offre['hotel_id'],
        $user_id,
        $ip,
        $ua
    ]);

    $clic_id = $pdo->lastInsertId();

    // URL de destination avec tracking
    $baseUrl = $offre['url'] ?: 'https://www.hotel-de-paris.fr';
    $separator = (strpos($baseUrl, '?') !== false) ? '&' : '?';
    $redirectUrl = $baseUrl . $separator . 'ref=lavache&offre=' . $offre['offre_id'] . '&clic=' . $clic_id;

    // Redirection
    header('Location: ' . $redirectUrl);
    exit;

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
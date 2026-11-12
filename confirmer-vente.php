<?php
// =============================================
// confirmer-vente.php - Vendue / Non vendue + 21 €
// =============================================

require_once 'auth.php';
requireRole('hotelier');
require_once 'config.php';

$clic_id = intval($_GET['clic_id'] ?? 0);
$action  = $_GET['action'] ?? '';

if ($clic_id <= 0 || !in_array($action, ['vendue', 'non_vendue'], true)) {
    die('Requête invalide.');
}

$user_id = $_SESSION['user_id'];

try {
    // Vérifier que le clic appartient à un hôtel de cet hôtelier
    $stmt = $pdo->prepare("
        SELECT c.*, o.id AS offre_id
        FROM clics_reservation c
        JOIN hotels h ON c.hotel_id = h.id
        JOIN offres o ON c.offre_id = o.id
        WHERE c.id = ? AND h.user_id = ? AND c.statut = 'clic'
    ");
    $stmt->execute([$clic_id, $user_id]);
    $clic = $stmt->fetch();

    if (!$clic) {
        die('Clic introuvable ou déjà traité.');
    }

    $pdo->beginTransaction();

    if ($action === 'vendue') {
        // 1. Statut clic
        $stmt = $pdo->prepare("
            UPDATE clics_reservation
            SET statut = 'vendue', confirme_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$clic_id]);

        // 2. Offre → reservee
        $stmt = $pdo->prepare("
            UPDATE offres SET statut = 'reservee' WHERE id = ?
        ");
        $stmt->execute([$clic['offre_id']]);

        // 3. Commission 21 €
        $stmt = $pdo->prepare("
            INSERT INTO commissions (clic_id, hotel_id, offre_id, montant, statut)
            VALUES (?, ?, ?, 21.00, 'a_payer')
        ");
        $stmt->execute([
            $clic_id,
            $clic['hotel_id'],
            $clic['offre_id']
        ]);

    } else {
        // Non vendue
        $stmt = $pdo->prepare("
            UPDATE clics_reservation
            SET statut = 'non_vendue', confirme_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$clic_id]);
    }

    $pdo->commit();
    header('Location: mes-reservations.php?ok=1');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die('Erreur : ' . $e->getMessage());
}
?>
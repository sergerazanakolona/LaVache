<?php
// =============================================
// mes-reservations.php - Clics à confirmer (Hôtelier)
// =============================================

require_once 'auth.php';
requireRole('hotelier');
require_once 'config.php';

$user_id = $_SESSION['user_id'];

// Hôtels de cet hôtelier
$stmt = $pdo->prepare("SELECT id, nom FROM hotels WHERE user_id = ?");
$stmt->execute([$user_id]);
$mesHotels = $stmt->fetchAll();
$hotel_ids = array_column($mesHotels, 'id');

if (empty($hotel_ids)) {
    $clics = [];
} else {
    $placeholders = implode(',', array_fill(0, count($hotel_ids), '?'));
    $sql = "
        SELECT c.*, o.type_chambre, o.prix, h.nom AS hotel_nom
        FROM clics_reservation c
        JOIN offres o ON c.offre_id = o.id
        JOIN hotels h ON c.hotel_id = h.id
        WHERE c.hotel_id IN ($placeholders)
        ORDER BY c.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($hotel_ids);
    $clics = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Mes réservations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f8f9fa; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-bold" href="hotelier.php">LaVache</a>
      <div class="d-flex align-items-center gap-3">
        <a href="hotelier.php" class="btn btn-outline-light btn-sm">Publier une offre</a>
        <a href="mes-reservations.php" class="btn btn-light btn-sm">Mes réservations</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Clics & réservations à confirmer</h4>
      <span class="badge bg-primary"><?= count($clics) ?> enregistrement(s)</span>
    </div>

    <?php if (isset($_GET['ok'])): ?>
      <div class="alert alert-success">Mise à jour effectuée.</div>
    <?php endif; ?>

    <?php if (empty($mesHotels)): ?>
      <div class="alert alert-warning">Aucun hôtel rattaché à votre compte.</div>
    <?php elseif (empty($clics)): ?>
      <div class="alert alert-info">Aucun clic pour le moment.</div>
    <?php else: ?>

      <div class="card shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th>Date</th>
                  <th>Hôtel</th>
                  <th>Offre</th>
                  <th>Prix</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($clics as $c): ?>
                  <tr>
                    <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
                    <td><?= htmlspecialchars($c['hotel_nom']) ?></td>
                    <td><?= htmlspecialchars($c['type_chambre']) ?></td>
                    <td><?= number_format($c['prix'], 0, ',', ' ') ?> €</td>
                    <td>
                      <?php if ($c['statut'] === 'clic'): ?>
                        <span class="badge bg-warning text-dark">En attente</span>
                      <?php elseif ($c['statut'] === 'vendue'): ?>
                        <span class="badge bg-success">Vendue</span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Non vendue</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($c['statut'] === 'clic'): ?>
                        <a href="confirmer-vente.php?clic_id=<?= (int)$c['id'] ?>&action=vendue"
                           class="btn btn-sm btn-success"
                           onclick="return confirm('Confirmer que cette chambre a été vendue via LaVache ?');">
                          Vendue
                        </a>
                        <a href="confirmer-vente.php?clic_id=<?= (int)$c['id'] ?>&action=non_vendue"
                           class="btn btn-sm btn-outline-secondary"
                           onclick="return confirm('Marquer comme non vendue ?');">
                          Non vendue
                        </a>
                      <?php else: ?>
                        <span class="text-muted small">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <p class="text-muted small mt-3">
        <strong>Vendue</strong> = le client a réservé grâce à LaVache → commission <strong>21 €</strong> enregistrée.<br>
        <strong>Non vendue</strong> = le clic n’a pas abouti.
      </p>

    <?php endif; ?>
  </div>

</body>
</html>

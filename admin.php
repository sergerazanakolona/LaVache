<?php
require_once 'auth.php';
requireRole('admin');
require_once 'config.php';

$nbHotels       = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$nbOffres       = $pdo->query("SELECT COUNT(*) FROM offres WHERE statut = 'active'")->fetchColumn();
$nbUsers        = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$nbReservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE DATE(date_reservation) = CURDATE()")->fetchColumn();

$stmt = $pdo->query("
    SELECT o.*, h.nom, h.logo 
    FROM offres o 
    JOIN hotels h ON o.hotel_id = h.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
");
$dernieresOffres = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Administration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f0f2f5; }
    .sidebar { width: 260px; min-height: 100vh; background: #0d6efd; }
    .sidebar .nav-link { color: rgba(255,255,255,0.85); border-radius: 0.5rem; margin-bottom: 0.25rem; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: white; }
    .stat-card { border-radius: 1rem; border: none; box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.06); }
    .logo-admin { width: 40px; height: 40px; object-fit: contain; }
    .clock { font-variant-numeric: tabular-nums; font-weight: 600; font-size: 1.1rem; }
  </style>
</head>
<body>
  <div class="d-flex">
    <div class="sidebar text-white p-3">
      <div class="d-flex align-items-center gap-2 mb-4 px-2">
        <img src="vache.png" alt="LaVache" class="logo-admin">
        <span class="fs-5 fw-bold">LaVache Admin</span>
      </div>
      <nav class="nav flex-column">
        <a class="nav-link active" href="admin.php"><i class="bi bi-speedometer2 me-2"></i> Tableau de bord</a>
        <a class="nav-link" href="hotels.php"><i class="bi bi-building me-2"></i> Hôtels</a>
        <a class="nav-link" href="ajouter-hotel.php"><i class="bi bi-plus-circle me-2"></i> Ajouter un hôtel</a>
        <a class="nav-link" href="utilisateurs.php"><i class="bi bi-people me-2"></i> Utilisateurs</a>
        <a class="nav-link" href="#"><i class="bi bi-tag me-2"></i> Offres</a>
        <a class="nav-link" href="#"><i class="bi bi-graph-up me-2"></i> Statistiques</a>
        <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i> Paramètres</a>
        <hr class="border-light my-3">
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
      </nav>
    </div>

    <div class="flex-grow-1 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Tableau de bord</h4>
        <div class="d-flex align-items-center gap-3">
          <span class="text-muted">Connecté en tant que <strong><?= htmlspecialchars($_SESSION['nom'] ?? 'Admin') ?></strong></span>
          <span class="badge bg-dark clock" id="liveClock">00:00:00</span>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between">
              <div><div class="text-muted small">Hôtels inscrits</div><div class="fs-3 fw-bold"><?= $nbHotels ?></div></div>
              <i class="bi bi-building fs-2 text-primary"></i>
            </div>
          </div></div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between">
              <div><div class="text-muted small">Offres actives</div><div class="fs-3 fw-bold"><?= $nbOffres ?></div></div>
              <i class="bi bi-tag fs-2 text-success"></i>
            </div>
          </div></div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between">
              <div><div class="text-muted small">Réservations du jour</div><div class="fs-3 fw-bold"><?= $nbReservations ?></div></div>
              <i class="bi bi-calendar-check fs-2 text-warning"></i>
            </div>
          </div></div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card"><div class="card-body">
            <div class="d-flex justify-content-between">
              <div><div class="text-muted small">Utilisateurs</div><div class="fs-3 fw-bold"><?= $nbUsers ?></div></div>
              <i class="bi bi-people fs-2 text-info"></i>
            </div>
          </div></div>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Dernières offres publiées</h5></div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Hôtel</th><th>Type</th><th>Prix</th><th>Expire</th><th>Statut</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($dernieresOffres)): ?>
                  <tr><td colspan="6" class="text-center text-muted py-4">Aucune offre</td></tr>
                <?php else: ?>
                  <?php foreach ($dernieresOffres as $offre): ?>
                    <tr>
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <img src="<?= htmlspecialchars($offre['logo'] ?? 'vache.png') ?>" width="32" height="32" class="rounded">
                          <?= htmlspecialchars($offre['nom']) ?>
                        </div>
                      </td>
                      <td><?= htmlspecialchars($offre['type_chambre']) ?></td>
                      <td><?= number_format($offre['prix'], 0, ',', ' ') ?> €</td>
                      <td><?= $offre['expire_le'] ?? 'Minuit' ?></td>
                      <td>
                        <?php if ($offre['statut'] === 'active'): ?>
                          <span class="badge bg-success">Active</span>
                        <?php else: ?>
                          <span class="badge bg-secondary"><?= htmlspecialchars($offre['statut']) ?></span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function updateClock() {
      const now = new Date();
      document.getElementById('liveClock').textContent =
        String(now.getHours()).padStart(2,'0') + ':' +
        String(now.getMinutes()).padStart(2,'0') + ':' +
        String(now.getSeconds()).padStart(2,'0');
    }
    updateClock();
    setInterval(updateClock, 1000);
  </script>
</body>
</html>
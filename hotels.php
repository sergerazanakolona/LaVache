<?php
require_once 'auth.php';
requireRole('admin');
require_once 'config.php';

$stmt = $pdo->query("
    SELECT h.*, u.email as email_proprietaire, u.nom as nom_proprietaire
    FROM hotels h
    LEFT JOIN users u ON h.user_id = u.id
    ORDER BY h.created_at DESC
");
$hotels = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Hôtels</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f0f2f5; }
    .sidebar { width: 260px; min-height: 100vh; background: #0d6efd; }
    .sidebar .nav-link { color: rgba(255,255,255,0.85); border-radius: 0.5rem; margin-bottom: 0.25rem; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: white; }
    .logo-admin { width: 40px; height: 40px; object-fit: contain; }
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
        <a class="nav-link" href="admin.php"><i class="bi bi-speedometer2 me-2"></i> Tableau de bord</a>
        <a class="nav-link active" href="hotels.php"><i class="bi bi-building me-2"></i> Hôtels</a>
        <a class="nav-link" href="ajouter-hotel.php"><i class="bi bi-plus-circle me-2"></i> Ajouter un hôtel</a>
        <a class="nav-link" href="utilisateurs.php"><i class="bi bi-people me-2"></i> Utilisateurs</a>
        <a class="nav-link" href="#"><i class="bi bi-tag me-2"></i> Offres</a>
        <hr class="border-light my-3">
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
      </nav>
    </div>

    <div class="flex-grow-1 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Liste des hôtels</h4>
        <div>
          <a href="ajouter-hotel.php" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-plus-circle"></i> Ajouter
          </a>
          <span class="badge bg-primary"><?= count($hotels) ?> hôtel(s)</span>
        </div>
      </div>

      <div class="card shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Logo</th><th>Nom</th><th>Étoiles</th><th>Adresse</th>
                  <th>Propriétaire</th><th>Téléphone</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($hotels)): ?>
                  <tr><td colspan="7" class="text-center text-muted py-4">Aucun hôtel</td></tr>
                <?php else: ?>
                  <?php foreach ($hotels as $hotel): ?>
                    <tr>
                      <td><img src="<?= htmlspecialchars($hotel['logo'] ?? 'vache.png') ?>" width="40" height="40" class="rounded"></td>
                      <td class="fw-semibold"><?= htmlspecialchars($hotel['nom']) ?></td>
                      <td><span class="text-warning"><?= str_repeat('★', (int)($hotel['etoiles'] ?? 0)) ?></span></td>
                      <td>
                        <?= htmlspecialchars($hotel['adresse']) ?><br>
                        <small class="text-muted"><?= htmlspecialchars(($hotel['code_postal'] ?? '') . ' ' . ($hotel['ville'] ?? '')) ?></small>
                      </td>
                      <td>
                        <?= htmlspecialchars($hotel['nom_proprietaire'] ?? '-') ?><br>
                        <small class="text-muted"><?= htmlspecialchars($hotel['email_proprietaire'] ?? '') ?></small>
                      </td>
                      <td><?= htmlspecialchars($hotel['telephone'] ?? '-') ?></td>
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
</body>
</html>
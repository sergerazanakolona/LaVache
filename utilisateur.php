<?php
require_once 'auth.php';
requireRole('admin');
require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Utilisateurs</title>
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
        <a class="nav-link" href="hotels.php"><i class="bi bi-building me-2"></i> Hôtels</a>
        <a class="nav-link" href="ajouter-hotel.php"><i class="bi bi-plus-circle me-2"></i> Ajouter un hôtel</a>
        <a class="nav-link active" href="utilisateurs.php"><i class="bi bi-people me-2"></i> Utilisateurs</a>
        <a class="nav-link" href="#"><i class="bi bi-tag me-2"></i> Offres</a>
        <hr class="border-light my-3">
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
      </nav>
    </div>

    <div class="flex-grow-1 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Liste des utilisateurs</h4>
        <span class="badge bg-primary"><?= count($users) ?> utilisateur(s)</span>
      </div>

      <div class="card shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th>
                  <th>Téléphone</th><th>Inscription</th><th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td><?= (int)$user['id'] ?></td>
                    <td class="fw-semibold">
                      <?= htmlspecialchars(trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''))) ?>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                      <?php if ($user['role'] === 'admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                      <?php elseif ($user['role'] === 'hotelier'): ?>
                        <span class="badge bg-primary">Hôtelier</span>
                      <?php else: ?>
                        <span class="badge bg-success">Voyageur</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></button>
                      <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
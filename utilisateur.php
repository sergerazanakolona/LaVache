<?php
require_once 'auth.php';
requireLogin();            // N’importe quel utilisateur connecté
// ou requireRole('voyageur');
?>
<?php
// =============================================
// utilisateur.php - Affiche les offres dynamiques
// =============================================

require_once 'config.php';

// Récupérer les offres actives avec les infos de l'hôtel
$sql = "SELECT o.*, h.nom, h.etoiles, h.adresse, h.code_postal, h.ville, 
               h.telephone, h.email, h.logo, h.url
        FROM offres o
        JOIN hotels h ON o.hotel_id = h.id
        WHERE o.statut = 'active'
        ORDER BY o.created_at DESC";

$stmt = $pdo->query($sql);
$offres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Offres de dernière minute</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f0f2f5; }
    .card-offer {
      border-radius: 1rem;
      max-width: 520px;
      margin: 0 auto 2rem auto;
    }
    .logo-hotel {
      width: 52px;
      height: 52px;
      object-fit: contain;
      border-radius: 8px;
    }
    .stars { font-size: 1.35rem; letter-spacing: 1px; }
    .price-badge { font-size: 1.05rem; padding: 0.45rem 0.9rem; }
    .clock { font-variant-numeric: tabular-nums; font-weight: 600; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">
        <i class="bi bi-building me-2"></i>LaVache
      </a>
      <span class="navbar-text text-white-50">Offres valables jusqu'à minuit</span>
    </div>
  </nav>

  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Offres de dernière minute</h4>
      <span class="badge bg-danger fs-6">
        <?= count($offres) ?> offre<?= count($offres) > 1 ? 's' : '' ?> disponible<?= count($offres) > 1 ? 's' : '' ?>
      </span>
    </div>

    <?php if (empty($offres)): ?>
      <div class="alert alert-info text-center">
        Aucune offre de dernière minute disponible pour le moment.
      </div>
    <?php else: ?>

      <?php foreach ($offres as $offre): ?>
        <div class="card card-offer shadow">
          <div class="card-body p-4">

            <!-- Ligne 0 -->
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center gap-2">
                <img src="<?= htmlspecialchars($offre['logo'] ?? 'vache.png') ?>" alt="Logo" class="logo-hotel">
                <div>
                  <div class="small text-muted" id="currentDate">Chargement...</div>
                  <div class="small fw-bold">
                    Expire à minuit | <span class="liveClock clock">00:00:00</span>
                  </div>
                </div>
              </div>
              <span class="badge bg-danger">Dernière minute</span>
            </div>

            <!-- Ligne 1 -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="fw-bold fs-5"><?= htmlspecialchars($offre['nom']) ?></span>
                <span class="text-warning stars ms-2">
                  <?= str_repeat('*', $offre['etoiles'] ?? 4) ?>
                </span>
              </div>
              <span class="badge bg-success-subtle text-success border border-success fw-semibold price-badge">
                <?= htmlspecialchars($offre['type_chambre']) ?> | <?= number_format($offre['prix'], 0, ',', ' ') ?> €
              </span>
            </div>

            <!-- Ligne 2 -->
            <div class="d-flex justify-content-center align-items-center gap-4 mb-4">
              <div class="text-muted small">
                <i class="bi bi-geo-alt"></i>
                <?= htmlspecialchars($offre['adresse']) ?>, 
                <?= htmlspecialchars($offre['ville']) ?> <?= htmlspecialchars($offre['code_postal']) ?>
              </div>
              <div class="d-flex gap-3">
                <?php if (!empty($offre['telephone'])): ?>
                  <a href="tel:<?= htmlspecialchars($offre['telephone']) ?>" class="text-decoration-none">
                    <i class="bi bi-telephone-fill text-primary fs-5"></i>
                  </a>
                <?php endif; ?>
                <?php if (!empty($offre['email'])): ?>
                  <a href="mailto:<?= htmlspecialchars($offre['email']) ?>" class="text-decoration-none">
                    <i class="bi bi-envelope-fill text-primary fs-5"></i>
                  </a>
                <?php endif; ?>
              </div>
            </div>

            <!-- Boutons -->
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-share"></i> Partager
              </button>
              <a href="<?= htmlspecialchars($offre['url'] ?? '#') ?>" 
                 target="_blank" 
                 class="btn btn-success flex-fill">
                <i class="bi bi-calendar-check"></i> Réserver
              </a>
            </div>

            <div class="text-center mt-2">
              <small class="text-muted">
                <i class="bi bi-shield-check text-success"></i>
                Lien sécurisé vers le site officiel de l’hôtel
              </small>
            </div>

          </div>
        </div>
      <?php endforeach; ?>

    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Date du jour
    const jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    const today = new Date();
    document.querySelectorAll('#currentDate, .small.text-muted').forEach(el => {
      if (el.id === 'currentDate' || el.textContent.includes('Chargement')) {
        el.textContent = `${jours[today.getDay()]} ${today.getDate()} ${mois[today.getMonth()]}`;
      }
    });

    // Horloge en temps réel
    function updateClock() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      document.querySelectorAll('.liveClock').forEach(el => {
        el.textContent = `${h}:${m}:${s}`;
      });
    }
    updateClock();
    setInterval(updateClock, 1000);
  </script>
</body>
</html>
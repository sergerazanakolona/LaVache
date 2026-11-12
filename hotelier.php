<?php
// =============================================
// hotelier.php - Publier une offre (hôtel au choix)
// =============================================

require_once 'auth.php';
requireRole('hotelier');
require_once 'config.php';

$user_id = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT id, nom, url, etoiles, adresse, code_postal, ville, telephone, email, logo
                       FROM hotels WHERE user_id = ? ORDER BY nom");
$stmt->execute([$user_id]);
$mesHotels = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Espace Hôtelier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .card-offer { border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,.08); }
    .preview-card { max-width: 520px; }
    .logo-hotel {
      width: 52px; height: 52px;
      object-fit: contain; border-radius: 8px;
    }
    .stars { font-size: 1.35rem; letter-spacing: 1px; }
    .price-badge { font-size: 1.05rem; padding: 0.45rem 0.9rem; }
    .clock { font-variant-numeric: tabular-nums; font-weight: 600; }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-bold" href="hotelier.php">LaVache</a>
      <div class="d-flex align-items-center gap-2">
        <span class="navbar-text text-white me-2">
          <?= htmlspecialchars($_SESSION['nom'] ?? 'Hôtelier') ?>
        </span>
        <a href="mes-reservations.php" class="btn btn-outline-light btn-sm">Mes réservations</a>
        <a href="logout.php" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row g-4">

      <!-- FORMULAIRE -->
      <div class="col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0">Publier une offre de dernière minute</h5>
          </div>
          <div class="card-body">

            <?php if (empty($mesHotels)): ?>
              <div class="alert alert-warning">
                Aucun hôtel n’est rattaché à votre compte.<br>
                Demandez à l’admin de lier un hôtel à votre utilisateur.
              </div>
            <?php else: ?>

            <form action="create-offre.php" method="POST">

              <!-- Choix de l'hôtel -->
              <div class="mb-3">
                <label class="form-label">Hôtel *</label>
                <select name="hotel_id" id="hotelSelect" class="form-select" required>
                  <option value="">— Choisir un hôtel —</option>
                  <?php foreach ($mesHotels as $h): ?>
                    <option
                      value="<?= (int)$h['id'] ?>"
                      data-nom="<?= htmlspecialchars($h['nom'], ENT_QUOTES) ?>"
                      data-etoiles="<?= (int)($h['etoiles'] ?? 4) ?>"
                      data-adresse="<?= htmlspecialchars(trim(($h['adresse'] ?? '') . ', ' . ($h['code_postal'] ?? '') . ' ' . ($h['ville'] ?? '')), ENT_QUOTES) ?>"
                      data-url="<?= htmlspecialchars($h['url'] ?? '', ENT_QUOTES) ?>"
                      data-tel="<?= htmlspecialchars($h['telephone'] ?? '', ENT_QUOTES) ?>"
                      data-email="<?= htmlspecialchars($h['email'] ?? '', ENT_QUOTES) ?>"
                      data-logo="<?= htmlspecialchars($h['logo'] ?? 'vache.png', ENT_QUOTES) ?>"
                    >
                      <?= htmlspecialchars($h['nom']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label text-muted small">Aperçu nom (auto)</label>
                <input type="text" class="form-control" id="infoNom" readonly placeholder="Choisissez un hôtel">
              </div>

              <div class="mb-3">
                <label class="form-label">Type de chambre *</label>
                <select class="form-select" name="type_chambre" id="roomType" required>
                  <option value="Double">Double</option>
                  <option value="Simple">Simple</option>
                  <option value="Suite">Suite</option>
                  <option value="Familiale">Familiale</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Prix (€) *</label>
                <input type="number" class="form-control" name="prix" id="price"
                       value="230" min="1" step="0.01" required>
              </div>

              <div class="mb-3">
                <label class="form-label">URL du site (bouton Réserver)</label>
                <input type="url" class="form-control" name="url" id="hotelUrl"
                       placeholder="https://www.hotel.fr">
              </div>

              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary flex-fill" disabled>
                  <i class="bi bi-share"></i> Partager
                </button>
                <button type="button" class="btn btn-outline-danger flex-fill" id="btnDelete">
                  <i class="bi bi-trash"></i> Supprimer
                </button>
                <button type="submit" class="btn btn-primary flex-fill">
                  <i class="bi bi-upload"></i> Publier
                </button>
              </div>

              <div class="form-text mt-2 text-center">
                L'offre sera valable jusqu'à minuit
              </div>
            </form>

            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- APERÇU -->
      <div class="col-lg-7">
        <h5 class="mb-3">Aperçu de l'offre</h5>
        <div class="card card-offer preview-card mx-auto">
          <div class="card-body px-4">

            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center gap-2">
                <img src="vache.png" alt="Logo" class="logo-hotel" id="previewLogo">
                <div>
                  <div class="small text-muted" id="currentDate">—</div>
                  <div class="small fw-bold">
                    Expire à minuit | <span id="liveClock" class="clock">00:00:00</span>
                  </div>
                </div>
              </div>
              <span class="badge bg-danger">Dernière minute</span>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="fw-bold fs-5" id="previewNom">Nom de l'hôtel</span>
                <span class="text-warning stars ms-2" id="previewStars">****</span>
              </div>
              <span class="badge bg-success-subtle text-success border border-success fw-semibold price-badge">
                <span id="previewRoom">Double</span> | <span id="previewPrice">230 €</span>
              </span>
            </div>

            <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
              <div class="text-muted small" id="previewAdresse">
                <i class="bi bi-geo-alt"></i> Adresse
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" disabled>
                <i class="bi bi-share"></i> Partager
              </button>
              <button type="button" class="btn btn-primary btn-sm flex-fill" disabled>
                <i class="bi bi-upload"></i> Publier
              </button>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Date + horloge
    const jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    const today = new Date();
    document.getElementById('currentDate').textContent =
      jours[today.getDay()] + ' ' + today.getDate() + ' ' + mois[today.getMonth()];

    function updateClock() {
      const now = new Date();
      document.getElementById('liveClock').textContent =
        String(now.getHours()).padStart(2,'0') + ':' +
        String(now.getMinutes()).padStart(2,'0') + ':' +
        String(now.getSeconds()).padStart(2,'0');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Changement d'hôtel → aperçu + URL
    document.getElementById('hotelSelect')?.addEventListener('change', function () {
      const opt = this.options[this.selectedIndex];
      if (!opt.value) return;

      document.getElementById('infoNom').value = opt.dataset.nom || '';
      document.getElementById('previewNom').textContent = opt.dataset.nom || 'Nom de l\'hôtel';
      const n = parseInt(opt.dataset.etoiles || '4', 10);
      document.getElementById('previewStars').textContent = '*'.repeat(n || 4);
      document.getElementById('previewAdresse').innerHTML =
        '<i class="bi bi-geo-alt"></i> ' + (opt.dataset.adresse || 'Adresse');
      document.getElementById('hotelUrl').value = opt.dataset.url || '';
      document.getElementById('previewLogo').src = opt.dataset.logo || 'vache.png';
    });

    // Aperçu type / prix
    document.getElementById('roomType')?.addEventListener('change', function () {
      document.getElementById('previewRoom').textContent = this.value;
    });
    document.getElementById('price')?.addEventListener('input', function () {
      document.getElementById('previewPrice').textContent = (this.value || '0') + ' €';
    });

    document.getElementById('btnDelete')?.addEventListener('click', function () {
      alert('La suppression d’annonce se fera depuis la liste des offres (à venir).');
    });
  </script>
</body>
</html>
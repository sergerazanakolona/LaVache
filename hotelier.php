<?php
// =============================================
// hotelier.php - Espace Hôtelier (protégé)
// =============================================

require_once 'auth.php';
requireRole('hotelier');   // Seuls les hôteliers peuvent accéder
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

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-bold" href="#">LaVache</a>
      <div class="d-flex align-items-center gap-3">
        <span class="navbar-text text-white">
          Bonjour <?= htmlspecialchars($_SESSION['nom'] ?? 'Hôtelier') ?>
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">
          <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="row g-4">

      <!-- ========== FORMULAIRE ========== -->
      <div class="col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0">Publier une offre de dernière minute</h5>
          </div>
          <div class="card-body">

            <form action="create-offre.php" method="POST">

              <div class="mb-3">
                <label class="form-label text-muted small">Nom de l'hôtel</label>
                <input type="text" class="form-control" value="Hotel de Paris" readonly>
              </div>

              <div class="mb-3">
                <label class="form-label text-muted small">Étoiles</label>
                <input type="text" class="form-control" value="****" readonly>
              </div>

              <div class="mb-3">
                <label class="form-label text-muted small">Adresse</label>
                <input type="text" class="form-control" value="17 rue Méridien, 75015 Paris" readonly>
              </div>

              <div class="mb-3">
                <label class="form-label">Type de chambre</label>
                <select class="form-select" name="type_chambre" id="roomType" required>
                  <option value="Double">Double</option>
                  <option value="Simple">Simple</option>
                  <option value="Suite">Suite</option>
                  <option value="Familiale">Familiale</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label">Prix (€)</label>
                <input type="number" class="form-control" name="prix" id="price" value="230" min="1" step="0.01" required>
              </div>

              <div class="mb-3">
                <label class="form-label">URL du site de l'hôtel</label>
                <input type="url" class="form-control" name="url" value="https://www.hotel-de-paris.fr">
              </div>

              <!-- 3 boutons -->
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary flex-fill">
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

          </div>
        </div>
      </div>

      <!-- ========== APERÇU ========== -->
      <div class="col-lg-7">
        <h5 class="mb-3">Aperçu de l'offre</h5>
        <div class="card card-offer preview-card mx-auto">
          <div class="card-body px-4">

            <!-- Ligne 0 -->
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center gap-2">
                <img src="vache.png" alt="Logo hôtel" class="logo-hotel">
                <div>
                  <div class="small text-muted" id="currentDate">Chargement...</div>
                  <div class="small fw-bold">
                    Expire à minuit | <span id="liveClock" class="clock">00:00:00</span>
                  </div>
                </div>
              </div>
              <span class="badge bg-danger">Dernière minute</span>
            </div>

            <!-- Ligne 1 -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="fw-bold fs-5">Hotel de Paris</span>
                <span class="text-warning stars ms-2">****</span>
              </div>
              <span class="badge bg-success-subtle text-success border border-success fw-semibold price-badge">
                <span id="previewRoom">Double</span> | <span id="previewPrice">230 €</span>
              </span>
            </div>

            <!-- Ligne 2 -->
            <div class="d-flex justify-content-center align-items-center gap-4 mb-3">
              <div class="text-muted small">
                <i class="bi bi-geo-alt"></i> 17 rue Méridien, Paris 75015
              </div>
              <div class="d-flex gap-3">
                <a href="tel:+33123456789" class="text-decoration-none">
                  <i class="bi bi-telephone-fill text-primary fs-5"></i>
                </a>
                <a href="mailto:contact@hotel-de-paris.fr" class="text-decoration-none">
                  <i class="bi bi-envelope-fill text-primary fs-5"></i>
                </a>
              </div>
            </div>

            <!-- 3 boutons aperçu -->
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary btn-sm flex-fill">
                <i class="bi bi-share"></i> Partager
              </button>
              <button class="btn btn-outline-danger btn-sm flex-fill" id="btnDeletePreview">
                <i class="bi bi-trash"></i> Supprimer
              </button>
              <button class="btn btn-primary btn-sm flex-fill">
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
    // Horloge en temps réel
    function updateClock() {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      document.getElementById('liveClock').textContent = `${h}:${m}:${s}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Date du jour
    const jours = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
    const mois = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
    const today = new Date();
    document.getElementById('currentDate').textContent = 
      `${jours[today.getDay()]} ${today.getDate()} ${mois[today.getMonth()]}`;

    // Aperçu en temps réel
    document.getElementById('roomType').addEventListener('change', function() {
      document.getElementById('previewRoom').textContent = this.value;
    });
    document.getElementById('price').addEventListener('input', function() {
      document.getElementById('previewPrice').textContent = (this.value || '0') + ' €';
    });

    // Suppression
    function supprimerAnnonce() {
      if (confirm('Voulez-vous vraiment supprimer cette annonce ?')) {
        alert('Annonce supprimée avec succès.');
      }
    }
    document.getElementById('btnDelete').addEventListener('click', supprimerAnnonce);
    document.getElementById('btnDeletePreview').addEventListener('click', supprimerAnnonce);
  </script>
</body>
</html>
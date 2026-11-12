<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache – Offres hôtels de dernière minute</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: #f0f2f5; }
    .hero {
      background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
      color: #fff;
      border-radius: 1.25rem;
      padding: 3rem 2rem;
    }
    .feature-icon {
      width: 48px; height: 48px;
      border-radius: 12px;
      background: rgba(13,110,253,.1);
      color: #0d6efd;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem;
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">
        <i class="bi bi-building me-1"></i> LaVache
      </a>
      <div class="d-flex gap-2">
        <a href="utilisateur.php" class="btn btn-outline-light btn-sm">Voir les offres</a>
        <a href="connexion.html" class="btn btn-light btn-sm">Connexion</a>
      </div>
    </div>
  </nav>

  <div class="container py-5">
    <div class="hero mb-5 text-center">
      <img src="vache.png" alt="LaVache" width="72" height="72" class="mb-3 rounded">
      <h1 class="fw-bold mb-3">Chambres d’hôtel à la dernière minute</h1>
      <p class="lead mb-4 opacity-90">
        Des offres limitées jusqu’à minuit, publiées par les hôtels partenaires.
      </p>
      <a href="utilisateur.php" class="btn btn-light btn-lg me-2">
        <i class="bi bi-search"></i> Voir les offres
      </a>
      <a href="connexion.html" class="btn btn-outline-light btn-lg">
        Se connecter
      </a>
    </div>

    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="feature-icon mx-auto mb-3"><i class="bi bi-clock"></i></div>
            <h5>Jusqu’à minuit</h5>
            <p class="text-muted mb-0">Chaque offre expire le soir même.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="feature-icon mx-auto mb-3"><i class="bi bi-shield-check"></i></div>
            <h5>Réservation chez l’hôtel</h5>
            <p class="text-muted mb-0">Vous payez sur le site officiel de l’hôtel.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-4">
            <div class="feature-icon mx-auto mb-3"><i class="bi bi-tag"></i></div>
            <h5>Code LAVACHE</h5>
            <p class="text-muted mb-0">Indiquez le code pour lier votre réservation.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="text-center text-muted py-4 small">
    © 2026 LaVache – Offres hôtels de dernière minute
  </footer>
</body>
</html>
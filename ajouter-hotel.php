<?php
// =============================================
// ajouter-hotel.php - Préenregistrer un hôtel (Admin)
// =============================================

require_once 'auth.php';
requireRole('admin');
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom'] ?? '');
    $etoiles     = intval($_POST['etoiles'] ?? 3);
    $adresse     = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville       = trim($_POST['ville'] ?? 'Paris');
    $telephone   = trim($_POST['telephone'] ?? '');
    $email_hotel = trim($_POST['email_hotel'] ?? '');
    $url         = trim($_POST['url'] ?? '');
    $logo        = trim($_POST['logo'] ?? 'vache.png');

    // Compte hôtelier (optionnel)
    $email_user  = trim($_POST['email_user'] ?? '');
    $nom_user    = trim($_POST['nom_user'] ?? '');
    $prenom_user = trim($_POST['prenom_user'] ?? '');
    $password    = $_POST['password'] ?? 'password';

    if ($nom === '' || $adresse === '') {
        $error = 'Le nom et l\'adresse de l\'hôtel sont obligatoires.';
    } else {
        try {
            $pdo->beginTransaction();

            $user_id = null;

            // Créer le compte hôtelier si email fourni
            if ($email_user !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password, role, nom, prenom, telephone)
                    VALUES (?, ?, 'hotelier', ?, ?, ?)
                ");
                $stmt->execute([$email_user, $hash, $nom_user, $prenom_user, $telephone]);
                $user_id = $pdo->lastInsertId();
            } else {
                // Sinon rattacher à un hôtelier existant (id=2 par défaut en test)
                // Tu pourras plus tard choisir dans une liste
                $user_id = 2;
            }

            $stmt = $pdo->prepare("
                INSERT INTO hotels (user_id, nom, etoiles, adresse, code_postal, ville, telephone, email, url, logo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $user_id, $nom, $etoiles, $adresse, $code_postal, $ville,
                $telephone, $email_hotel, $url, $logo
            ]);

            $pdo->commit();
            $message = 'Hôtel préenregistré avec succès.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LaVache - Préenregistrer un hôtel</title>
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
        <a class="nav-link" href="utilisateurs.php"><i class="bi bi-people me-2"></i> Utilisateurs</a>
        <a class="nav-link" href="ajouter-hotel.php"><i class="bi bi-plus-circle me-2"></i> Ajouter un hôtel</a>
        <hr class="border-light my-3">
        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Déconnexion</a>
      </nav>
    </div>

    <div class="flex-grow-1 p-4">
      <h4 class="mb-4">Préenregistrer un hôtel</h4>

      <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="card shadow-sm">
        <div class="card-body">
          <form method="POST">
            <h5 class="mb-3">Informations hôtel</h5>
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Nom de l'hôtel *</label>
                <input type="text" name="nom" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Étoiles</label>
                <select name="etoiles" class="form-select">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3" selected>3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Adresse *</label>
                <input type="text" name="adresse" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Code postal</label>
                <input type="text" name="code_postal" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label">Ville</label>
                <input type="text" name="ville" class="form-control" value="Paris">
              </div>
              <div class="col-md-4">
                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Email hôtel</label>
                <input type="email" name="email_hotel" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">URL site web (pour Réserver)</label>
                <input type="url" name="url" class="form-control" placeholder="https://www.hotel.fr">
              </div>
              <div class="col-md-6">
                <label class="form-label">Logo (nom fichier)</label>
                <input type="text" name="logo" class="form-control" value="vache.png">
              </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Compte hôtelier (optionnel)</h5>
            <p class="text-muted small">Si tu remplis ces champs, un compte hôtelier est créé automatiquement.</p>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Email de connexion</label>
                <input type="email" name="email_user" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Mot de passe</label>
                <input type="text" name="password" class="form-control" value="password">
              </div>
              <div class="col-md-6">
                <label class="form-label">Nom</label>
                <input type="text" name="nom_user" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom_user" class="form-control">
              </div>
            </div>

            <div class="mt-4">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Préenregistrer l'hôtel
              </button>
              <a href="hotels.php" class="btn btn-outline-secondary">Retour à la liste</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
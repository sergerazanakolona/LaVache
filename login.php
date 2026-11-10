<?php
// =============================================
// login.php - Connexion utilisateur
// =============================================

session_start();
require_once 'config.php';

// On accepte uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.html');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation simple
if (empty($email) || empty($password)) {
    echo "<script>
            alert('Email et mot de passe obligatoires');
            window.location.href = 'connexion.html';
          </script>";
    exit;
}

try {
    // Recherche de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {

        // Connexion réussie → on crée la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['nom']     = $user['nom'];

        // Redirection selon le rôle
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } elseif ($user['role'] === 'hotelier') {
            header('Location: hotelier.php');
        } else {
            header('Location: utilisateur.php');
        }
        exit;

    } else {
        // Identifiants incorrects
        echo "<script>
                alert('Email ou mot de passe incorrect');
                window.location.href = 'connexion.html';
              </script>";
        exit;
    }

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
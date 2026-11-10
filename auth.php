<?php
// =============================================
// auth.php - Sécurisation des pages
// =============================================

session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.html');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        // Redirige vers la page correspondant à son vrai rôle
        if ($_SESSION['role'] === 'admin') {
            header('Location: admin.php');
        } elseif ($_SESSION['role'] === 'hotelier') {
            header('Location: hotelier.php');
        } else {
            header('Location: utilisateur.php');
        }
        exit;
    }
}
?>
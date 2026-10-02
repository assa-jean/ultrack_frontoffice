<?php
// new/app/Controllers/AuthController.php

require_once __DIR__ . '/../../config/database.php';

// 1. Afficher le formulaire de connexion
function showLoginForm() {
    // Si l'utilisateur est déjà connecté, on le redirige selon son rôle
    if (isset($_SESSION['user_id'])) {
        redirectByRole();
    }
    require_once __DIR__ . '/../Views/auth/login.php';
}

// 2. Traitement de la connexion
function login() {
    global $db;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($username) && !empty($password)) {
            // Recherche de l'utilisateur en BD
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérification du mot de passe
            if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
                // Stockage des informations en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = strtoupper($user['role']);
                $_SESSION['flotte'] = $user['flotte'] ?? '';

                redirectByRole();
            } else {
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
                require_once __DIR__ . '/../Views/auth/login.php';
            }
        } else {
            $error = "Veuillez remplir tous les champs.";
            require_once __DIR__ . '/../Views/auth/login.php';
        }
    }
}

// 3. Redirection automatique selon le Rôle
function redirectByRole() {
    $role = $_SESSION['role'] ?? 'SUPERVISEUR';
    if ($role === 'ADMIN') {
        header('Location: index.php?route=dashboard');
    } else {
        header('Location: index.php?route=front_register');
    }
    exit;
}

// 4. Déconnexion
function logout() {
    session_destroy();
    header('Location: index.php?route=login');
    exit;
}
?>
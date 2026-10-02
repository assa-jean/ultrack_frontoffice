<?php
// new/app/Controllers/RoleController.php

require_once __DIR__ . '/../../config/database.php';

// 1. Afficher la liste des utilisateurs et leurs rôles
function manageRoles() {
    global $db;

    // Sécurité : Vérifier si l'utilisateur connecté est administrateur
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?route=login');
        exit;
    }

    // Récupérer la liste de tous les utilisateurs
    $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Charger la vue dédiée aux droits
    require_once __DIR__ . '/../Views/backoffice/droits.php';
}

// 2. Mettre à jour le rôle ou les droits d'un utilisateur
function updateRole() {
    global $db;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        $role = htmlspecialchars($_POST['role']);

        $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $userId]);

        header('Location: index.php?route=roles&success=1');
        exit;
    }
}



// 3. AJOUTER UN NOUVEAU SUPERVISEUR
// 3. AJOUTER UN NOUVEAU SUPERVISEUR AVEC UPLOAD CNI
function addSupervisor() {
    global $db;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = htmlspecialchars($_POST['username']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $enseigne = htmlspecialchars($_POST['enseigne']);
        $flotte = htmlspecialchars($_POST['flotte']);
        $role = htmlspecialchars($_POST['role']);
        $password = $_POST['password'];

        // Gestion des uploads de fichiers (CNI Recto et Verso)
        $cni_recto_name = '';
        $cni_verso_name = '';
        $uploadDir = __DIR__ . '/../../old/cni_pictures/'; // Dossier cible pour les images CNI

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Upload CNI Recto
        if (isset($_FILES['cni_recto']) && $_FILES['cni_recto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cni_recto']['name'], PATHINFO_EXTENSION);
            $cni_recto_name = 'recto_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['cni_recto']['tmp_name'], $uploadDir . $cni_recto_name);
        }

        // Upload CNI Verso
        if (isset($_FILES['cni_verso']) && $_FILES['cni_verso']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cni_verso']['name'], PATHINFO_EXTENSION);
            $cni_verso_name = 'verso_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['cni_verso']['tmp_name'], $uploadDir . $cni_verso_name);
        }

        // Insertion en base de données
        $stmt = $db->prepare("INSERT INTO users (username, telephone, enseigne, flotte, role, password, cni_recto, cni_verso, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $telephone, $enseigne, $flotte, $role, $password, $cni_recto_name, $cni_verso_name]);

        header('Location: index.php?route=roles&success=1');
        exit;
    }
}

// 4. METTRE À JOUR UN SUPERVISEUR
function updateSupervisor() {
    global $db;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        $id = (int)$_POST['user_id'];
        $username = htmlspecialchars($_POST['username']);
        $telephone = htmlspecialchars($_POST['telephone']);
        $enseigne = htmlspecialchars($_POST['enseigne']);
        $flotte = htmlspecialchars($_POST['flotte']);
        $role = htmlspecialchars($_POST['role']);
        $password = $_POST['password'];

        // Gestion optionnelle des nouveaux fichiers CNI
        $uploadDir = __DIR__ . '/../../old/cni_pictures/';
        
        // Récupérer les anciens fichiers pour éventuellement les nettoyer si besoin
        $stmtOld = $db->prepare("SELECT cni_recto, cni_verso FROM users WHERE id = ?");
        $stmtOld->execute([$id]);
        $oldFiles = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $cni_recto_name = $oldFiles['cni_recto'];
        if (isset($_FILES['cni_recto']) && $_FILES['cni_recto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cni_recto']['name'], PATHINFO_EXTENSION);
            $cni_recto_name = 'recto_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['cni_recto']['tmp_name'], $uploadDir . $cni_recto_name);
        }

        $cni_verso_name = $oldFiles['cni_verso'];
        if (isset($_FILES['cni_verso']) && $_FILES['cni_verso']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['cni_verso']['name'], PATHINFO_EXTENSION);
            $cni_verso_name = 'verso_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            move_uploaded_file($_FILES['cni_verso']['tmp_name'], $uploadDir . $cni_verso_name);
        }

        // Mise à jour (on met à jour le mot de passe seulement s'il est renseigné)
        if (!empty($password)) {
            $stmt = $db->prepare("UPDATE users SET username = ?, telephone = ?, enseigne = ?, flotte = ?, role = ?, password = ?, cni_recto = ?, cni_verso = ? WHERE id = ?");
            $stmt->execute([$username, $telephone, $enseigne, $flotte, $role, $password, $cni_recto_name, $cni_verso_name, $id]);
        } else {
            $stmt = $db->prepare("UPDATE users SET username = ?, telephone = ?, enseigne = ?, flotte = ?, role = ?, cni_recto = ?, cni_verso = ? WHERE id = ?");
            $stmt->execute([$username, $telephone, $enseigne, $flotte, $role, $cni_recto_name, $cni_verso_name, $id]);
        }

        header('Location: index.php?route=roles&success=2');
        exit;
    }
}



?>


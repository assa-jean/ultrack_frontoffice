<?php
session_start();

// ROUTEUR CENTRAL
$route = $_GET['route'] ?? 'login';

switch ($route) {
    // AUTHENTIFICATION
    case 'login':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            login();
        } else {
            showLoginForm();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        logout();
        break;
		
    // FRONT-OFFICE (SUPERVISEUR TERRAIN)
    case 'front_register':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        showRegisterForm();
        break;

    case 'submit_agent':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        submitAgent();
        break;

    default:
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        showLoginForm();
        break;

    // FRONT OFFICE
    case 'front_register':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        showRegisterForm();
        break;

    case 'front_stats':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        showSuperviseurStats();
        break;

    case 'submit_agent':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        submitAgent();
        break;

    case 'search_famoco':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        searchFamoco();
        break;

    case 'check_duplicate':
        require_once __DIR__ . '/../app/Controllers/FrontController.php';
        checkDuplicate();
        break;

    
}
?>
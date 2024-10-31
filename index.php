<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once('config/database.php');
require_once('controllers/AuthController.php');
require_once('controllers/DashboardController.php');
require_once('controllers/CommandeController.php');

$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Pages accessibles sans authentification
$public_pages = ['login'];

// Vérification de l'authentification
if (!isset($_SESSION['user_id']) && !in_array($action, $public_pages)) {
    header('Location: index.php?action=login');
    exit();
}

// Si l'utilisateur est connecté et essaie d'accéder à la page de login
if (isset($_SESSION['user_id']) && in_array($action, $public_pages)) {
    header('Location: index.php?action=dashboard');
    exit();
}

// Router
switch($action) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;
    
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    // Routes pour les commandes
    case 'commandes':
        $controller = new CommandeController();
        $controller->index();
        break;

    case 'commandes/create':
        $controller = new CommandeController();
        $controller->create();
        break;

    case 'commandes/edit':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $controller = new CommandeController();
        $controller->edit($id);
        break;

    case 'commandes/delete':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $controller = new CommandeController();
        $controller->delete($id);
        break;

    case 'commandes/view':
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $controller = new CommandeController();
        $controller->view($id);
        break;

        // Nouvelles routes pour l'import/export
        case 'commandes/import':
            $controller = new CommandeController();
            $controller->import();
            break;

        case 'commandes/template':
            $controller = new CommandeController();
            $controller->template();
            break;

        case 'commandes/export':
            $format = $_GET['format'] ?? 'excel';
            $controller = new CommandeController();
            $controller->export($format);
            break;
        
    case 'commandes/updateStatus':
        $controller = new CommandeController();
        $controller->updateStatus();
        break;

    case 'commandes/tracking':
        $controller = new CommandeController();
        $controller->tracking();
        break;
        
        case 'commandes/confirmed':
    $controller = new CommandeController();
    $controller->confirmed();
    break;

    case 'commandes/shipping':
    $controller = new CommandeController();
    $controller->shipping();
    break;

    // Routes pour les expéditions
case 'shipping':
    require_once('controllers/ShippingController.php');
    $controller = new ShippingController();
    $controller->index();
    break;

case 'shipping/shipped':
    require_once('controllers/ShippingController.php');
    $controller = new ShippingController();
    $controller->shipped();
    break;

case 'shipping/updateStatus':
    require_once('controllers/ShippingController.php');
    $controller = new ShippingController();
    $controller->updateStatus();
    break;

    case 'shipping/confirmed':
    require_once('controllers/ShippingController.php');
    $controller = new ShippingController();
    $controller->confirm();
    break;
        
    default:
        header('Location: index.php?action=dashboard');
        break;
}
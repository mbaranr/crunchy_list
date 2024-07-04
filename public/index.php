<?php

// Bootstrap the application
require_once __DIR__ . '/../app/bootstrap.php';

// Route the request
$route = $_GET['route'] ?? '/';

$uc = App\Controllers\UserController::getInstance();
$uc->handleUserState($route); // Handle user state transitions (logout, login, registration)

include 'includes/header.php';
include 'includes/navigation.php';

switch ($route) {
    case '/':
    case 'home':
        $controller = App\Controllers\HomeController::getInstance();
        echo $controller->index();
        break;
    case 'favorites':
        $controller = App\Controllers\FavoriteController::getInstance();
        echo $controller->index();
        break;
    case 'statistics':
        $controller = App\Controllers\StatisticsController::getInstance();
        echo $controller->index();
        break;
    case 'anime':
        $controller = App\Controllers\AnimeController::getInstance();
        echo $controller->index();
        break;
    case 'login':
    case 'register':
        echo $uc->index($route);
        break;
    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}

include 'includes/footer.php';
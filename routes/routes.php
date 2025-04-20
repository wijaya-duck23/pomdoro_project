<?php
// Routes configuration

// Include controller
require_once __DIR__ . '/../app/controllers/PomodoroController.php';

// Create controller instance
$controller = new PomodoroController();

// Handle route based on request method and path
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path if necessary
$basePath = '/';  // Adjust if app is in a subdirectory
$route = str_replace($basePath, '', $requestUri);

// Define routes
switch ($requestMethod) {
    case 'GET':
        if ($route === '' || $route === 'index.php') {
            // Redirect to the main dashboard
            header('Location: dashboard.php');
            exit;
        } elseif ($route === 'dashboard.php') {
            $controller->showDashboard();
        } elseif ($route === 'api/sessions') {
            $controller->getSessions();
        }
        break;
    
    case 'POST':
        if ($route === 'api/saveSession') {
            $controller->saveSession();
        }
        break;
        
    default:
        // Handle 404
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Route not found']);
        break;
} 
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$container = require __DIR__ . '/../config/container.php';
$routes = require __DIR__ . '/../config/routes.php';

// Get the request URI and remove any query string
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base path from URI if exists
$basePath = '/';  // Update this if your API is not at the root
$uri = substr($uri, strlen($basePath));

// Ensure URI starts with /
$uri = '/' . ltrim($uri, '/');

$method = $_SERVER['REQUEST_METHOD'];

// Parse query parameters
$queryParams = [];
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $queryParams);
}

// Handle OPTIONS request for CORS
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit(0);
}

// Add CORS headers for other requests
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

try {
    // Check if the route exists
    if (isset($routes[$method][$uri])) {
        $routeConfig = $routes[$method][$uri];
        $controller = $container->get($routeConfig['controller']);
        $action = $routeConfig['method'];

        // Handle request body for POST requests
        $params = [];
        if ($method === 'POST') {
            $requestBody = json_decode(file_get_contents('php://input'), true) ?? [];

            // Validate required parameters
            foreach ($routeConfig['params'] as $param) {
                if (!isset($requestBody[$param])) {
                    throw new \Exception("Missing required parameter: $param");
                }
                $params[$param] = $requestBody[$param];
            }
        } else {
            $params = $queryParams;
        }

        // Execute the controller action
        $result = $controller->$action($params);

        echo json_encode($result);
    } else {
        // Route not found
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} catch (\Exception $e) {
    // Handle errors
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
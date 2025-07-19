<?php
// Simple front controller and router for MVC structure

// Get the requested path
$request = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string for routing
if (($qpos = strpos($request, '?')) !== false) {
    $request = substr($request, 0, $qpos);
}

// Remove leading/trailing slashes
$request = trim($request, '/');

// Default route
if ($request === '' || $request === 'index.php') {
    require_once __DIR__ . '/app/Controllers/index.php';
    exit;
}

// Map static assets
if (preg_match('#^(css|js|img)/#', $request)) {
    return false; // Let the web server handle static files
}

// Try to route to a controller
$controllerPath = __DIR__ . '/app/Controllers/' . $request;
if (is_dir($controllerPath)) {
    $controllerPath .= '/dashboard.php'; // Default to dashboard in subdirs
} elseif (is_file($controllerPath . '.php')) {
    $controllerPath .= '.php';
}

if (file_exists($controllerPath)) {
    require_once $controllerPath;
    exit;
}

// 404 Not Found
http_response_code(404);
echo '404 Not Found';

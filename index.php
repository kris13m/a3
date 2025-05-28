<?php

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/app/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($prefix, $class, strlen($prefix)) !== 0) continue;

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_readable($file)) {
            require $file;
            return;
        }
    }
});


header('Content-Type: application/json');

// Get method
$method = $_SERVER['REQUEST_METHOD'];

// Parse the URI path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove base folder from path
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim($scriptName, '/');
$cleanUri = preg_replace("#^$basePath#", '', $uri);
$cleanUri = trim($cleanUri, '/');

// Split into segments
$segments = explode('/', $cleanUri);
$resource = $segments[0] ?? null;

$controllerPath = __DIR__ . "/app/controllers/{$resource}.php";

print_r($controllerPath);

if (file_exists($controllerPath)) {
    global $segments, $method;
    include $controllerPath;
} else {
    http_response_code(404);
    echo json_encode(["error" => "Resource not found"]);
}
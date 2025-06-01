<?php

require_once __DIR__ . '/app/Config/env_loader.php';
loadEnv(__DIR__ . '/.env');

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => __DIR__ . '/app/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) continue;

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_readable($file)) {
            require $file;
            return;
        }
    }
});

use App\Core\Logger;

header('Content-Type: application/json');

// Get method (initial)
$method = $_SERVER['REQUEST_METHOD'];

// Support _method override for PUT/DELETE via POST
if ($method === 'POST') {
    // For form-encoded data
    if (isset($_POST['_method'])) {
        $method = strtoupper($_POST['_method']);
    }
    // For JSON payloads
    else {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['_method'])) {
            $method = strtoupper($input['_method']);
        }
    }
}

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

// --- Logger setup and logging (together) ---

$logger = new Logger(__DIR__ . '/logs/requests.log');
$ip = $_SERVER['REMOTE_ADDR'];
$body = file_get_contents('php://input');

$logger->log("[$method] $uri from $ip. Body: $body");

// --- Controller routing ---

$controllerPath = __DIR__ . "/app/controllers/{$resource}.php";

// Optional debug print (comment out or remove when stable)
// print_r($controllerPath);

if (file_exists($controllerPath)) {
    global $segments, $method;
    include $controllerPath;
} else {
    http_response_code(404);
    echo json_encode(["error" => "Resource not found"]);
}
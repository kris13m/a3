<?php

namespace App\Controllers;

use App\Models\Genre;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$genres = Genre::getAll();
echo json_encode($genres);
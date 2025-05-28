<?php

namespace App\Controllers;

use App\Models\Track;

header('Content-Type: application/json');

$id = $segments[1] ?? null;




switch ($method) {
    case 'GET':
        if ($id) {
            $track = Track::getById($id);
            echo json_encode($track);
        } elseif (isset($_GET['s'])) {
            $tracks = Track::searchByName($_GET['s']);
            echo json_encode($tracks);
        } elseif (isset($_GET['composer'])) {
            $tracks = Track::getByComposer($_GET['composer']);
            echo json_encode($tracks);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing query parameter']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'] ?? null;
        $albumId = $data['album_id'] ?? null;
        $mediaTypeId = $data['media_type_id'] ?? null;
        $genreId = $data['genre_id'] ?? null;
        $composer = $data['composer'] ?? null;
        $milliseconds = $data['milliseconds'] ?? null;
        $bytes = $data['bytes'] ?? null;
        $unitPrice = $data['unit_price'] ?? null;

        if ($name && $albumId && $mediaTypeId && $genreId && $milliseconds !== null && $bytes !== null && $unitPrice !== null) {
            $trackId = Track::create($name, $albumId, $mediaTypeId, $genreId, $composer, $milliseconds, $bytes, $unitPrice);
            echo json_encode(['id' => $trackId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
        }
        break;

    case 'PUT':
        if ($id) {
            $data = json_decode(file_get_contents('php://input'), true);

            $name = $data['name'] ?? null;
            $albumId = $data['album_id'] ?? null;
            $mediaTypeId = $data['media_type_id'] ?? null;
            $genreId = $data['genre_id'] ?? null;
            $composer = $data['composer'] ?? null;
            $milliseconds = $data['milliseconds'] ?? null;
            $bytes = $data['bytes'] ?? null;
            $unitPrice = $data['unit_price'] ?? null;

            Track::update($id, $name, $albumId, $mediaTypeId, $genreId, $composer, $milliseconds, $bytes, $unitPrice);
            echo json_encode(['message' => 'Track updated']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing track id']);
        }
        break;

    case 'DELETE':
        if ($id) {
            if (Track::isInPlaylist($id)) {
                http_response_code(409);
                echo json_encode(['error' => 'Cannot delete track belonging to playlist']);
            } else {
                Track::delete($id);
                echo json_encode(['message' => 'Track deleted']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing track id']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
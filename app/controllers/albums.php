<?php

namespace App\Controllers;

use App\Models\Album;

header('Content-Type: application/json');

$id = $segments[1] ?? null;
$subresource = $segments[2] ?? null;



switch ($method) {
    case 'GET':
        if ($id && $subresource === 'tracks') {
            $tracks = Album::getTracksByAlbumId($id);
            echo json_encode($tracks);
        } elseif ($id) {
            $album = Album::getById($id);
            echo json_encode($album);
        } elseif (isset($_GET['s'])) {
            $albums = Album::searchByTitle($_GET['s']);
            echo json_encode($albums);
        } else {
            $albums = Album::getAll();
            echo json_encode($albums);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $title = $data['title'] ?? null;
        $artistId = $data['artist_id'] ?? null;

        if ($title && $artistId) {
            $albumId = Album::create($title, $artistId);
            echo json_encode(['id' => $albumId]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing title or artist_id']);
        }
        break;

    case 'PUT':
        if ($id) {
            $data = json_decode(file_get_contents('php://input'), true);
            $title = $data['title'] ?? null;
            $artistId = $data['artist_id'] ?? null;

            Album::update($id, $title, $artistId);
            echo json_encode(['message' => 'Album updated']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing album id']);
        }
        break;

    case 'DELETE':
        if ($id) {
            if (Album::hasTracks($id)) {
                http_response_code(409);
                echo json_encode(['error' => 'Cannot delete album with tracks']);
            } else {
                Album::delete($id);
                echo json_encode(['message' => 'Album deleted']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing album id']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
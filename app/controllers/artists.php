<?php

namespace App\Controllers;

use App\Models\Artist;

header('Content-Type: application/json');

$id = $segments[1] ?? null;
$subresource = $segments[2] ?? null;



switch ($method) {
    case 'GET':
        if ($id && $subresource === 'albums') {
            print_r("subresource: " . $subresource);
            $artist = Artist::getAlbumsByArtistId($id);
            echo json_encode($artist);
        } elseif ($id) {
            $artist = Artist::getById($id);
            echo json_encode($artist);
        } elseif (isset($_GET['s'])) {
            $artists = Artist::searchByName($_GET['s']);
            echo json_encode($artists);
        } else {
            $artists = Artist::getAll();
            echo json_encode($artists);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'] ?? $_POST['name'] ?? null;

        if ($name) { // check if name exists
            if (Artist::existsByName($name)) {
                http_response_code(409); 
                echo json_encode(['error' => 'Artist name already exists']);
            } else {
                $artist = Artist::create($name);
                echo json_encode($artist);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing artist name']);
        }
        break;

    case 'DELETE':
        if ($id) {
            if (Artist::hasAlbums($id)) {
                http_response_code(409); // Conflict code
                echo json_encode(['error' => 'Cannot delete artist with existing albums']);
            } else {
                $success = Artist::delete($id);
                if ($success) {
                    echo json_encode(['message' => 'Artist deleted']);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Artist not found']);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing artist id']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
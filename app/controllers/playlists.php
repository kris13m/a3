<?php

namespace App\Controllers;

use App\Models\Playlist;

header('Content-Type: application/json');

$id = $segments[1] ?? null;
$subresource = $segments[2] ?? null;
$subresourceId = $segments[3] ?? null;

switch ($method) {
    case 'GET':
    if ($id) {
        // GET playlists/<playlist_id> — Retrieve one playlist INCLUDING its tracks
        $playlist = Playlist::getByIdWithTracks($id);
        if ($playlist) {
            echo json_encode($playlist);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Playlist not found']);
        }
    } elseif (isset($_GET['s'])) {
        // GET playlists?s=<search_text> — Retrieve playlists matching search text
        $playlists = Playlist::searchByName($_GET['s']);
        echo json_encode($playlists);
    } else {
        // GET playlists — Retrieve all playlists
        $playlists = Playlist::getAll();
        echo json_encode($playlists);
    }
    break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if ($id && $subresource === 'tracks') {
            $trackId = $data['track_id'] ?? null;
            if (!$trackId) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing track_id']);
                break;
            }
            if (Playlist::addTrackToPlaylist($id, $trackId)) {
                echo json_encode(['message' => 'Track added to playlist']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to add track']);
            }
            break;
        }

        if (!$id) {
            $name = $data['name'] ?? null;
            if (!$name) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing playlist name']);
                break;
            }
            $playlistId = Playlist::create($name);
            echo json_encode(['id' => $playlistId]);
            break;
        }

        http_response_code(400);
        echo json_encode(['error' => 'Invalid POST request']);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing playlist id']);
            break;
        }

        if ($subresource === 'tracks') {
            $trackId = $subresourceId;
            if (!$trackId) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing track id']);
                break;
            }

            if (Playlist::removeTrackFromPlaylist($id, $trackId)) {
                echo json_encode(['message' => 'Track removed from playlist']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to remove track']);
            }
        } else {
            if (Playlist::hasTracks($id)) {
                http_response_code(409);
                echo json_encode(['error' => 'Cannot delete playlist with tracks']);
            } else {
                Playlist::delete($id);
                echo json_encode(['message' => 'Playlist deleted']);
            }
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
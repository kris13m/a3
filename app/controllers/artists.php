<?php

namespace App\Controllers;


use App\Models\Artist;


header('Content-Type: application/json');


print_r($method);

$id = $segments[1] ?? null;
$subresource = $segments[2] ?? null;

switch ($method) {
    case 'GET':
        if ($id && $subresource === 'albums') {
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
        // POST artists
        // Expect JSON or form data with 'name'
        break;

    case 'DELETE':
        if ($id) {
            // DELETE artists/<id>
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
<?php

namespace App\Models;

use App\Core\Model;


class Album extends Model
{
    public static function getAlbumsByArtistId(int $artistId): array
    {
    $sql = "SELECT * FROM album WHERE artistId = :artistId";
    $params = ['artistId' => $artistId];
    return self::execute($sql, $params);
    }
}
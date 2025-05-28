<?php

namespace App\Models;

use App\Core\Model;

class Artist extends Model
{

    public static function getall(): array
    {
        $sql = "SELECT * FROM artist";
        return self::execute($sql);
    }



    public static function getById(int $artistId): array|null
{
    $sql = "SELECT * FROM artist WHERE artistId = :artistId";
    $params = ['artistId' => $artistId];
    $results = self::execute($sql, $params);

    if (!empty($results)) {
        return $results[0];  // first row only
    }
    return null;
}

    public static function searchByName(string $searchText): array
    {
        $sql = "SELECT * FROM artist WHERE Name LIKE :searchText";
        $params = ['searchText' => '%' . $searchText . '%'];
        return self::execute($sql, $params);
    }

    public static function getAlbumsByArtistId(int $artistId): array
    {
    $sql = "SELECT * FROM album WHERE artistId = :artistId";
    $params = ['artistId' => $artistId];
    return self::execute($sql, $params);
    }
}
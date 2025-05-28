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

    public static function create(string $name): array
    {
        $sql = "INSERT INTO artist (Name) VALUES (:name)";
        $params = ['name' => $name];
        $id = self::execute($sql, $params);

        return [
            'id' => (int)$id,
            'name' => $name
        ];
    }

    public static function existsByName(string $name): bool
    {
        $sql = "SELECT COUNT(*) FROM artist WHERE Name = :name";
        $params = ['name' => $name];
        $result = self::execute($sql, $params);
        
        return $result[0]['COUNT(*)'] > 0;
    }

    public static function hasAlbums(int $artistId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM album WHERE ArtistId = :artistId";
        $params = ['artistId' => $artistId];
        $result = self::execute($sql, $params);
        return $result[0]['count'] > 0;
    }

    public static function delete(int $artistId): bool
    {
        $sql = "DELETE FROM artist WHERE ArtistId = :artistId";
        $params = ['artistId' => $artistId];
        return self::execute($sql, $params) > 0;
    }
}
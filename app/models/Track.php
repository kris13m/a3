<?php

namespace App\Models;

use App\Core\Model;


class Track extends Model
{
    public static function getById(int $id): array|false
    {
        $sql = "SELECT t.*, mt.Name AS MediaTypeName, g.Name AS GenreName
                FROM track t
                LEFT JOIN mediatype mt ON t.MediaTypeId = mt.MediaTypeId
                LEFT JOIN genre g ON t.GenreId = g.GenreId
                WHERE t.TrackId = :id";
        $result = self::execute($sql, ['id' => $id]);
        return $result[0] ?? false;
    }

    public static function searchByName(string $search): array
    {
        $sql = "SELECT t.*, mt.Name AS MediaTypeName, g.Name AS GenreName
                FROM track t
                LEFT JOIN mediatype mt ON t.MediaTypeId = mt.MediaTypeId
                LEFT JOIN genre g ON t.GenreId = g.GenreId
                WHERE t.Name LIKE :search";
        return self::execute($sql, ['search' => "%$search%"]);
    }

    public static function getByComposer(string $composer): array
    {
        $sql = "SELECT t.*, mt.Name AS MediaTypeName, g.Name AS GenreName
                FROM track t
                LEFT JOIN mediatype mt ON t.MediaTypeId = mt.MediaTypeId
                LEFT JOIN genre g ON t.GenreId = g.GenreId
                WHERE t.Composer = :composer";
        return self::execute($sql, ['composer' => $composer]);
    }

    public static function create(
        string $name,
        int $albumId,
        int $mediaTypeId,
        int $genreId,
        ?string $composer,
        int $milliseconds,
        int $bytes,
        float $unitPrice
    ): int {
        $sql = "INSERT INTO track
                (Name, AlbumId, MediaTypeId, GenreId, Composer, Milliseconds, Bytes, UnitPrice)
                VALUES (:name, :albumId, :mediaTypeId, :genreId, :composer, :milliseconds, :bytes, :unitPrice)";
        return self::execute($sql, [
            'name' => $name,
            'albumId' => $albumId,
            'mediaTypeId' => $mediaTypeId,
            'genreId' => $genreId,
            'composer' => $composer,
            'milliseconds' => $milliseconds,
            'bytes' => $bytes,
            'unitPrice' => $unitPrice
        ]);
    }

    public static function update(
        int $id,
        ?string $name = null,
        ?int $albumId = null,
        ?int $mediaTypeId = null,
        ?int $genreId = null,
        ?string $composer = null,
        ?int $milliseconds = null,
        ?int $bytes = null,
        ?float $unitPrice = null
    ): bool {
        $fields = [];
        $params = ['id' => $id];

        if ($name !== null) {
            $fields[] = "Name = :name";
            $params['name'] = $name;
        }
        if ($albumId !== null) {
            $fields[] = "AlbumId = :albumId";
            $params['albumId'] = $albumId;
        }
        if ($mediaTypeId !== null) {
            $fields[] = "MediaTypeId = :mediaTypeId";
            $params['mediaTypeId'] = $mediaTypeId;
        }
        if ($genreId !== null) {
            $fields[] = "GenreId = :genreId";
            $params['genreId'] = $genreId;
        }
        if ($composer !== null) {
            $fields[] = "Composer = :composer";
            $params['composer'] = $composer;
        }
        if ($milliseconds !== null) {
            $fields[] = "Milliseconds = :milliseconds";
            $params['milliseconds'] = $milliseconds;
        }
        if ($bytes !== null) {
            $fields[] = "Bytes = :bytes";
            $params['bytes'] = $bytes;
        }
        if ($unitPrice !== null) {
            $fields[] = "UnitPrice = :unitPrice";
            $params['unitPrice'] = $unitPrice;
        }

        if (empty($fields)) { // nothing to update exception
            return false;
        }

        $sql = "UPDATE track SET " . implode(', ', $fields) . " WHERE TrackId = :id";
        self::execute($sql, $params);
        return true;
    }

    public static function delete(int $id): int
    {
        $sql = "DELETE FROM track WHERE TrackId = :id";
        return self::execute($sql, ['id' => $id]);
    }

    public static function isInPlaylist(int $id): bool
    {
        $sql = "SELECT 1 FROM playlisttrack WHERE TrackId = :id LIMIT 1";
        $result = self::execute($sql, ['id' => $id]);
        return !empty($result);
    }
}
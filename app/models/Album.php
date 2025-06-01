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

    public static function getAll(): array
    {
        $sql = "SELECT album.AlbumId, album.Title, album.ArtistId, artist.Name AS ArtistName
                FROM album
                JOIN artist ON album.ArtistId = artist.ArtistId";
        return static::execute($sql);
    }

    public static function getById(int $id): ?array
    {
        $sql = "SELECT album.AlbumId, album.Title, album.ArtistId, artist.Name AS ArtistName
                FROM album
                JOIN artist ON album.ArtistId = artist.ArtistId
                WHERE album.AlbumId = :id
                LIMIT 1";
        $result = static::execute($sql, ['id' => $id]);
        return $result[0] ?? null;
    }

    public static function searchByTitle(string $searchText): array
    {
        $sql = "SELECT album.AlbumId, album.Title, album.ArtistId, artist.Name AS ArtistName
                FROM album
                JOIN artist ON album.ArtistId = artist.ArtistId
                WHERE album.Title LIKE :search";
        return static::execute($sql, ['search' => '%' . $searchText . '%']);
    }

    public static function getTracksByAlbumId(int $albumId): array
{
    $sql = "
        SELECT 
            track.TrackId,
            track.Name,
            
            mediatype.Name AS MediaType,
            
            genre.Name AS Genre,
            track.Composer,
            track.Milliseconds,
            track.Bytes,
            track.UnitPrice
        FROM track
        JOIN mediatype ON track.MediaTypeId = mediatype.MediaTypeId
        JOIN genre ON track.GenreId = genre.GenreId
        WHERE track.AlbumId = :albumId
    ";
    
    return static::execute($sql, ['albumId' => $albumId]);
}

    public static function create(string $title, int $artistId): int
    {
        $sql = "INSERT INTO album (Title, ArtistId) VALUES (:title, :artistId)";
        return static::execute($sql, ['title' => $title, 'artistId' => $artistId]);
    }

    public static function update(int $id, ?string $title = null, ?int $artistId = null): void
    {
        $fields = [];
        $params = ['id' => $id];

        if ($title !== null) {
            $fields[] = 'Title = :title';
            $params['title'] = $title;
        }
        if ($artistId !== null) {
            $fields[] = 'ArtistId = :artistId';
            $params['artistId'] = $artistId;
        }

        if (!empty($fields)) {
            $sql = "UPDATE album SET " . implode(', ', $fields) . " WHERE AlbumId = :id";
            static::execute($sql, $params);
        }
    }

    public static function hasTracks(int $albumId): bool
    {
        $sql = "SELECT 1 FROM track WHERE AlbumId = :albumId LIMIT 1";
        $result = static::execute($sql, ['albumId' => $albumId]);
        return !empty($result);
    }

    public static function delete(int $id): int
    {
        $sql = "DELETE FROM album WHERE AlbumId = :id";
        return static::execute($sql, ['id' => $id]);
    }

}
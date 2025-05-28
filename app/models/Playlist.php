<?php

namespace App\Models;

use App\Core\Model;

class Playlist extends Model
{


    public static function getByIdWithTracks($id): array|false
    {
        $sql = "SELECT * FROM playlist WHERE PlaylistId = :id";
        $playlist = self::execute($sql, ['id' => $id]);
        if (empty($playlist)) {
            return false;
        }

        $playlist = $playlist[0];

        $sqlTracks = "
            SELECT t.TrackId, t.Name
            FROM track t
            INNER JOIN playlisttrack pt ON t.TrackId = pt.TrackId
            WHERE pt.PlaylistId = :id
        ";
        $tracks = self::execute($sqlTracks, ['id' => $id]);

        $playlist['tracks'] = $tracks;

        return $playlist;
    }

    public static function searchByName(string $search): array
    {
        $sql = "SELECT * FROM playlist WHERE Name LIKE :search";
        return self::execute($sql, ['search' => '%' . $search . '%']);
    }

    public static function getAll(): array
{
    $sql = "SELECT * FROM playlist";
    return self::execute($sql);
}

    public static function create(string $name): int
    {
        $sql = "INSERT INTO playlist (Name) VALUES (:name)";
        return self::execute($sql, ['name' => $name]);
    }

    public static function addTrackToPlaylist(int $playlistId, int $trackId): bool
    {
        $sql = "INSERT INTO playlisttrack (PlaylistId, TrackId) VALUES (:playlistId, :trackId)";
        self::execute($sql, ['playlistId' => $playlistId, 'trackId' => $trackId]);
        return true;
    }

    public static function removeTrackFromPlaylist(int $playlistId, int $trackId): int
    {
        $sql = "DELETE FROM playlisttrack WHERE PlaylistId = :playlistId AND TrackId = :trackId";
        return self::execute($sql, ['playlistId' => $playlistId, 'trackId' => $trackId]);
    }

    public static function hasTracks(int $playlistId): bool
    {
        $sql = "SELECT 1 FROM playlisttrack WHERE PlaylistId = :playlistId LIMIT 1";
        $result = self::execute($sql, ['playlistId' => $playlistId]);
        return !empty($result);
    }

    public static function delete(int $playlistId): int
    {
        $sql = "DELETE FROM playlist WHERE PlaylistId = :playlistId";
        return self::execute($sql, ['playlistId' => $playlistId]);
    }
}
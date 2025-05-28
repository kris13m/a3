<?php

namespace App\Models;

use App\Core\Model;

class Genre extends Model
{
    public static function getAll(): array
    {
        $sql = "SELECT GenreId, Name FROM genre ORDER BY Name";
        return self::execute($sql);
    }
}
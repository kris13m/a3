<?php

namespace App\Models;

use App\Core\Model;

class MediaType extends Model
{
    public static function getAll(): array
    {
        $sql = "SELECT * FROM mediatype";
        return self::execute($sql);
    }
}
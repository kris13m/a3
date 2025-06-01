<?php

namespace App\Config;

class Config {
    public static function getDbHost(): string {
        return getenv('DB_HOST') ?: 'localhost';
    }
    public static function getDbName(): string {
        return getenv('DB_NAME') ?: 'chinook_autoincrement';
    }
    public static function getDbUser(): string {
        return getenv('DB_USER') ?: 'root';
    }
    public static function getDbPassword(): string {
        return getenv('DB_PASSWORD') ?: 'root';
    }
}
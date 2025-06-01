<?php

/**
 * base model
 */

 namespace App\Core;

 use PDO;
 use PDOException;
 use App\Config\Config;

 abstract class Model
 {
    protected static function getDB(): PDO
{
    static $db = null;

    if ($db === null) {
        try {
            $dsn = 'mysql:host=' . Config::getDbHost() .
                   ';dbname=' . Config::getDbName() . 
                   ';charset=utf8';

            $db = new PDO($dsn, Config::getDbUser(), Config::getDbPassword());
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \Exception("Error <strong>{$e->getMessage()}</strong> in model " . get_called_class());
        }
    }

    return $db;
}
    /**
     * for selects, it will return the query results as an associative array
     * for inserts, it will return the new pk value
     * for deletes, it will return whether any rows have been affected
     * 
     */

    protected static function execute(string $sql, array $params = []): array|int
    {
        $db = static::getDB();

        if (empty($params)) {
            $stmt = $db->query($sql);
        } else {
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            $stmt->execute();
        }

        switch(substr(ltrim($sql), 0, 6)){
            case 'SELECT':
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            case 'INSERT':
                return $db->lastInsertId();
            case 'DELETE':
                return $stmt->rowCount();
            default:
                return 0;
            
        }
    }
 }
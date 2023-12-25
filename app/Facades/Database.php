<?php
declare(strict_types=1);

namespace Kento1221\UserUsergroupCrudApp\Facades;

use PDO;

class Database
{
    public static function getConnection(): PDO
    {
        $host = 'mariadb'; //localhost:3306 if run by non-docker composer
        $db = 'db';
        $user = 'db';
        $pass = 'db';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}
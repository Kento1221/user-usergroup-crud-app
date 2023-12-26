<?php

namespace Kento1221\UserUsergroupCrudApp\Helpers;

use Kento1221\UserUsergroupCrudApp\Facades\Database;
use PDO;

class UserHelper
{
    public static function checkIfUserExists(int $id): bool
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM users WHERE id = :id;";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
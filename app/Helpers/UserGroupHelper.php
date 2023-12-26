<?php

namespace Kento1221\UserUsergroupCrudApp\Helpers;

use Kento1221\UserUsergroupCrudApp\Facades\Database;
use PDO;

class UserGroupHelper
{
    /**
     * @param array $groupIds
     * @return bool
     * @throws \Exception
     */
    public static function checkIfAllGroupIdsExistInDatabase(array $groupIds): bool
    {
        $db = Database::getConnection();
        $wildcards = implode(',', array_fill(0, count($groupIds), '?'));

        $query = "SELECT id FROM user_groups WHERE id IN ($wildcards)";
        $stmt = $db->prepare($query);

        $stmt->execute($groupIds);
        $existingGroups = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        return !array_diff($groupIds, $existingGroups);
    }

    public static function checkIfNameExistsInDatabase(string $name): bool
    {
        $db = Database::getConnection();
        $query = "SELECT id FROM user_groups WHERE name = :name;";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public static function checkIfGroupIdExistsInDatabase(int $id): bool
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM user_groups WHERE id = :id;";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

}
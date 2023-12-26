<?php

namespace Kento1221\UserUsergroupCrudApp\Helpers;

use Kento1221\UserUsergroupCrudApp\Facades\Database;
use PDO;

class UserGroupHelper
{
    /**
     * @param array $groupIds
     * @return void
     * @throws \Exception
     */
    public static function checkIfAllGroupIdsExistInDatabase(array $groupIds): void
    {
        $db = Database::getConnection();
        $wildcards = implode(',', array_fill(0, count($groupIds), '?'));

        $query = "SELECT id FROM user_groups WHERE id IN ($wildcards)";
        $stmt = $db->prepare($query);

        $stmt->execute($groupIds);
        $existingGroups = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $missingGroupIds = array_diff($groupIds, $existingGroups);
        if ($missingGroupIds) {
            throw new \Exception('Not all groups exist in the database. GroupIds that do not exist: '
                . implode(', ', $missingGroupIds) . '.'
            );
        }
    }
}
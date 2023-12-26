<?php

namespace Kento1221\UserUsergroupCrudApp\Validators;

use Kento1221\UserUsergroupCrudApp\Facades\Database;
use PDO;

class StoreUserRequestValidator implements RequestValidator
{
    /**
     * @throws \Exception
     */
    public static function validate(): array
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $groups = filter_var_array($_POST['groups'] ?? [], FILTER_VALIDATE_INT);

        if ($groups) {
            self::checkIfAllGroupIdsExist($groups);
        }

        if (!$email || !$first_name || !$last_name || !$date_of_birth || !$password) {
            throw new \Exception(
                'Missing parameters. Required parameters of email, first_name, last_name, date_of_birth, password.'
            );
        }

        return [
            'email'         => $email,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'date_of_birth' => $date_of_birth,
            'password'      => $password,
            'groups'        => $groups
        ];
    }

    /**
     * @param $groups
     * @return void
     * @throws \Exception
     */
    public static function checkIfAllGroupIdsExist($groups): void
    {
        $db = Database::getConnection();
        $wildcards = implode(',', array_fill(0, count($groups), '?'));

        $query = "SELECT id FROM user_groups WHERE id IN ($wildcards)";
        $stmt = $db->prepare($query);

        $stmt->execute($groups);
        $existingGroups = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $missingGroupIds = array_diff($groups, $existingGroups);
        if ($missingGroupIds) {
            throw new \Exception('Not all groups exist in the database. GroupID that does not exist: '
                . implode(', ', $missingGroupIds)
            );
        }
    }
}
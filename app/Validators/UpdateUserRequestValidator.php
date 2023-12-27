<?php

namespace Kento1221\UserUsergroupCrudApp\Validators;

use Kento1221\UserUsergroupCrudApp\Helpers\UserGroupHelper;
use Kento1221\UserUsergroupCrudApp\Helpers\UserHelper;

class UpdateUserRequestValidator implements RequestValidator
{
    /**
     * @throws \Exception
     */
    public static function validate(): array
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
        $groups = filter_var_array($_POST['groups'] ?? [], FILTER_VALIDATE_INT);

        if ($groups) {
            $allExist = UserGroupHelper::checkIfAllGroupIdsExistInDatabase($groups);

            if (!$allExist) {
                throw new \Exception('Not all groups exist in the database.');
            }
        }

        if (!$id || !$name || !$first_name || !$last_name || !$date_of_birth) {
            throw new \Exception(
                'Missing parameters. Required parameters of id, username, first_name, last_name, date_of_birth.'
            );
        }

        if (!UserHelper::checkIfUserExists($id)) {
            throw new \Exception("The user of id `$id` does not exist.");
        }

        return [
            'id'            => $id,
            'name'          => $name,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'date_of_birth' => $date_of_birth,
            'groups'        => $groups ?? []
        ];
    }
}
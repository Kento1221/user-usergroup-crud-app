<?php

namespace Kento1221\UserUsergroupCrudApp\Validators;

use Kento1221\UserUsergroupCrudApp\Helpers\UserGroupHelper;

class UpdateUserRequestValidator implements RequestValidator
{
    /**
     * @throws \Exception
     */
    public static function validate(): array
    {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $date_of_birth = filter_input(INPUT_POST, 'date_of_birth', FILTER_SANITIZE_STRING);
        $groups = filter_var_array($_POST['groups'] ?? [], FILTER_VALIDATE_INT);

        if ($groups) {
            UserGroupHelper::checkIfAllGroupIdsExistInDatabase($groups);
        }

        if (!$id || !$email || !$first_name || !$last_name || !$date_of_birth) {
            throw new \Exception(
                'Missing parameters. Required parameters of id, email, first_name, last_name, date_of_birth.'
            );
        }

        return [
            'id'            => $id,
            'email'         => $email,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'date_of_birth' => $date_of_birth,
            'groups'        => $groups ?? []
        ];
    }
}
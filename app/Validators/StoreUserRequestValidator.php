<?php

namespace Kento1221\UserUsergroupCrudApp\Validators;

use Kento1221\UserUsergroupCrudApp\Helpers\UserGroupHelper;

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
            $allExist = UserGroupHelper::checkIfAllGroupIdsExistInDatabase($groups);

            if (!$allExist) {
                throw new \Exception('Not all groups exist in the database.');
            }
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
}
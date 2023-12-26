<?php

namespace Kento1221\UserUsergroupCrudApp\Validators;

use Kento1221\UserUsergroupCrudApp\Helpers\UserGroupHelper;

class UpdateGroupRequestValidator implements RequestValidator
{
    /**
     * @throws \Exception
     */
    public static function validate(): array
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

        if (!$name) {
            throw new \Exception('Missing name parameter.');
        }

        if (!UserGroupHelper::checkIfGroupIdExistsInDatabase($id)) {
            throw new \Exception('Provided group id does not exist.');
        };

        if (UserGroupHelper::checkIfNameExistsInDatabase($name)) {
            throw new \Exception('Provided group name already exists.');
        };

        return [
            'id'   => $id,
            'name' => $name
        ];
    }
}
<?php

namespace Kento1221\UserUsergroupCrudApp\Validators;

use Kento1221\UserUsergroupCrudApp\Helpers\UserGroupHelper;

class StoreGroupRequestValidator implements RequestValidator
{
    /**
     * @throws \Exception
     */
    public static function validate(): array
    {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

        if (!$name) {
            throw new \Exception('Missing name parameter.');
        }

        if (UserGroupHelper::checkIfNameExistsInDatabase($name)) {
            throw new \Exception('Provided name already exists.');
        };

        return [
            'name' => $name
        ];
    }
}
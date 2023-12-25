<?php
declare(strict_types=1);

namespace Kento1221\UserUsergroupCrudApp\Models;

class User extends Model
{
    protected array $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'date_of_birth'
    ];

    protected array $hidden = [
        'password'
    ];
}
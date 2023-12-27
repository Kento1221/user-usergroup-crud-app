<?php
declare(strict_types=1);

namespace Kento1221\UserUsergroupCrudApp\Models;

use Kento1221\UserUsergroupCrudApp\Facades\Database;
use PDO;

class User extends Model
{
    protected ?string $table    = 'users';
    protected array   $fillable = [
        'name',
        'password',
        'first_name',
        'last_name',
        'date_of_birth'
    ];

    protected array $hidden = [
        'password'
    ];

    /**
     * @throws \Exception
     */
    public function groups(): array
    {
        $db = Database::getConnection();
        $query = "SELECT ug.* FROM user_groups ug 
                  INNER JOIN user_user_groups uug ON ug.id = uug.user_group_id 
                  WHERE uug.user_id = :userId";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':userId', $this->getKey(), PDO::PARAM_INT);

        $stmt->execute();
        $groupModel = (new UserGroup())->with(['created_at', 'updated_at']);

        return array_map(
            fn(array $group) => $groupModel->hydrateModel($group),
            $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []
        );
    }
}
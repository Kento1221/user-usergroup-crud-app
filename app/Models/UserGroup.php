<?php

namespace Kento1221\UserUsergroupCrudApp\Models;

use Kento1221\UserUsergroupCrudApp\Facades\Database;
use PDO;

class UserGroup extends Model
{
    protected ?string $table = 'user_groups';

    protected array $fillable = [
        'name'
    ];

    public function users(): array
    {
        $db = Database::getConnection();
        $query = "SELECT u.* FROM users u 
                  INNER JOIN user_user_groups uug ON u.id = uug.user_id 
                  WHERE uug.user_group_id = :groupId";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':groupId', $this->getKey(), PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
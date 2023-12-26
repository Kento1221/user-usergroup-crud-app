<?php

namespace Kento1221\UserUsergroupCrudApp\Models;

class Model
{
    protected ?string $table       = null;
    protected string  $primaryKey  = 'id';
    protected array   $fillable    = [];
    protected array   $withColumns = [];
    protected array   $hidden      = [];

    protected function getFieldsToShow(bool $withHidden = false): array
    {
        return [
            $this->getKeyName(),
            ...array_filter($this->fillable, fn($field) => $withHidden ? $field : !in_array($field, $this->hidden)),
            ...$this->withColumns
        ];
    }

    protected function getKeyName(): string
    {
        return $this->primaryKey;
    }

    protected function getKey(): int
    {
        return $this->{$this->getKeyName()};
    }

    protected function getTable(): string
    {
        $path = explode('\\', static::class);
        return $this->table ?? strtolower(array_pop($path)) . 's';
    }

    public function getAll(): array
    {
        $fields = implode(', ', $this->getFieldsToShow());
        $table = $this->getTable();

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $data = $db->query("SELECT $fields FROM $table;")->fetchAll();
        return array_map(fn($modelData) => $this->hydrateModel($modelData), $data);
    }

    public function get(int $limit = 10, int $offset = 0): array
    {
        $fields = implode(', ', $this->getFieldsToShow());
        $table = $this->getTable();
        $primaryKey = $this->getKeyName();

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $data = $db->query("SELECT $fields FROM $table ORDER BY $primaryKey LIMIT $limit OFFSET $offset;")
                   ->fetchAll();
        return array_map(fn($modelData) => $this->hydrateModel($modelData), $data);
    }

    /**
     * @throws \Exception
     */
    public function getById(int $id): self
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Provided id parameter of invalid value.');
        }

        $fields = implode(', ', $this->getFieldsToShow());
        $table = $this->getTable();
        $primaryKey = $this->getKeyName();

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $data = $db->query("SELECT $fields FROM $table WHERE $primaryKey = $id;")->fetch();

        if (!$data) {
            throw new \Exception("User of id `$id` not found.");
        }

        return $this->hydrateModel($data);
    }

    private function hydrateModel(array $data, bool $withHidden = false): self
    {
        $model = new static();
        $fields = $this->getFieldsToShow($withHidden);

        foreach ($fields as $field) {
            $model->$field = $data[$field] ?? null;
        }

        return $model;
    }

    public function update(int $id, array $data): bool
    {
        try {
            $table = $this->getTable();
            $primaryKey = $this->getKeyName();
            $parameters = [];
            $valuesToBind = [];

            foreach ($data as $field => $value) {
                if ($field !== $primaryKey && in_array($field, $this->fillable)) {
                    $parameters[] = "$field = :$field";
                    $valuesToBind[":$field"] = $value;
                }
            }

            $parametersString = implode(', ', $parameters);

            $query = "UPDATE $table SET $parametersString WHERE $primaryKey = :primaryKey";
            $valuesToBind[':primaryKey'] = $id;

            $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
            $stmt = $db->prepare($query);

            return $stmt->execute($valuesToBind);

        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function with(array $columns): self
    {
        $this->withColumns = $columns;

        return $this;
    }

    public function delete(int $id): bool
    {
        $table = $this->getTable();
        $primaryKey = $this->getKeyName();

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        return (bool)$db->exec("DELETE FROM $table WHERE $primaryKey = $id;");
    }

    /**
     * Create a model using data array. If model was created, the new instance will be returned.
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function create(array $data): self
    {
        $table = $this->getTable();
        $dataKeys = array_keys($data);
        $primaryKey = $this->getKeyName();
        $columns = [];
        $placeholders = [];
        $valuesToBind = [];

        foreach ($dataKeys as $dataKey) {
            if ($dataKey !== $primaryKey && in_array($dataKey, $this->fillable)) {
                $columns[] = $dataKey;
                $placeholders[] = ":$dataKey";
                $valuesToBind[":$dataKey"] = $data[$dataKey];
            }
        }

        $columnsString = implode(', ', $columns);
        $placeholdersString = implode(', ', $placeholders);

        $query = "INSERT INTO $table ($columnsString) VALUES ($placeholdersString)";
        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $stmt = $db->prepare($query);

        if ($stmt->execute($valuesToBind)) {
            $id = $db->lastInsertId();
            unset($db);

            return $this->getById($id);
        }

        throw new \Exception('New user could not be created');
    }

    /**
     * Attach related model id in many-to-many relationship table.
     * @param int $relatedKeyValue ID value of related model.
     * @param string $foreignTable the name of many-to-many relationship-linking table.
     * @param string $foreignTableLocalKey the colum name in the $foreignTable that holds called class's id.
     * @param string $foreignTableRelatedKey the column name in the $foreignTable that holds related model's id.
     * @return bool
     */
    public function append(
        int    $relatedKeyValue,
        string $foreignTable,
        string $foreignTableLocalKey,
        string $foreignTableRelatedKey
    ): bool {
        $query = "INSERT INTO $foreignTable ($foreignTableLocalKey, $foreignTableRelatedKey) 
                  VALUES (?, ?) 
                  ON DUPLICATE UPDATE id = id;";

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $stmt = $db->prepare($query);

        return $stmt->execute($relatedKeyValue);
    }

    /**
     * Attach many related model ids in many-to-many relationship table.
     * @param array $relatedKeyValues ID values of related model.
     * @param string $foreignTable the name of many-to-many relationship-linking table.
     * @param string $foreignTableLocalKey the colum name in the $foreignTable that holds called class's id.
     * @param string $foreignTableRelatedKey the column name in the $foreignTable that holds related model's id.
     * @return bool
     */
    public function appendMany(
        array  $relatedKeyValues,
        string $foreignTable,
        string $foreignTableLocalKey,
        string $foreignTableRelatedKey
    ): bool {

        $wildcards = implode(', ', array_fill(0, count($relatedKeyValues), '(?, ?)')) ?: '?';
        $query = "
            INSERT INTO $foreignTable ($foreignTableLocalKey, $foreignTableRelatedKey) 
            VALUES $wildcards 
            ON DUPLICATE KEY UPDATE id = id;";

        $values = [];
        foreach ($relatedKeyValues ?: [0] as $relatedKeyValue) {
            $values[] = $this->getKey();
            $values[] = $relatedKeyValue;
        }

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $stmt = $db->prepare($query);

        return $stmt->execute($values);
    }

    /**
     * Sync (detach and attach) many related model ids in many-to-many relationship table.
     * @param array $relatedKeyValues ID values of related model.
     * @param string $foreignTable the name of many-to-many relationship-linking table.
     * @param string $foreignTableLocalKey the colum name in the $foreignTable that holds called class's id.
     * @param string $foreignTableRelatedKey the column name in the $foreignTable that holds related model's id.
     * @return bool
     * @throws \Exception
     */
    public function sync(
        array  $relatedKeyValues,
        string $foreignTable,
        string $foreignTableLocalKey,
        string $foreignTableRelatedKey
    ): bool {

        $detached = $this->detachMany(
            $relatedKeyValues,
            $foreignTable,
            $foreignTableLocalKey,
            $foreignTableRelatedKey
        );

        if (!$detached) {
            throw new \Exception('Could not detach ids of: ' . implode(', ', $relatedKeyValues));
        }

        if (empty($relatedKeyValues)) {
            return true;
        }

        $attached = $this->appendMany(
            $relatedKeyValues,
            $foreignTable,
            $foreignTableLocalKey,
            $foreignTableRelatedKey
        );

        if (!$attached) {
            throw new \Exception('Could not attach ids of: ' . implode(', ', $relatedKeyValues));
        }

        return true;
    }

    /**
     * Detach many related model ids from many-to-many relationship table.
     * @param array $relatedKeyValues ID values of related model.
     * @param string $foreignTable the name of many-to-many relationship-linking table.
     * @param string $foreignTableLocalKey the colum name in the $foreignTable that holds called class's id.
     * @param string $foreignTableRelatedKey the column name in the $foreignTable that holds related model's id.
     * @return bool
     */
    public function detachMany(
        array  $relatedKeyValues,
        string $foreignTable,
        string $foreignTableLocalKey,
        string $foreignTableRelatedKey
    ): bool {
        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();

        $ids = implode(',', array_fill(0, count($relatedKeyValues), '?')) ?: '?';
        $deleteQuery = "
            DELETE FROM $foreignTable 
            WHERE $foreignTableLocalKey = ? 
            AND $foreignTableRelatedKey NOT IN ($ids)";

        $deleteStmt = $db->prepare($deleteQuery);
        return $deleteStmt->execute([$this->getKey(), ...($relatedKeyValues ?: [0])]);
    }

    public function detach(
        int    $relatedKeyValue,
        string $foreignTable,
        string $foreignTableLocalKey,
        string $foreignTableRelatedKey
    ): bool {

        $db = \Kento1221\UserUsergroupCrudApp\Facades\Database::getConnection();
        $deleteQuery = "
            DELETE FROM $foreignTable 
            WHERE $foreignTableLocalKey = ? 
            AND $foreignTableRelatedKey = ?";

        $deleteStmt = $db->prepare($deleteQuery);
        return $deleteStmt->execute([$this->getKey(), $relatedKeyValue]);
    }
}
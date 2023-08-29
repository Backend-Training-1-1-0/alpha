<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Contracts\DatabaseConnectionInterface;
use PDO;

class MySqlConnection extends PDO implements DatabaseConnectionInterface
{

    function exec(string $query, array $bindings = []): int
    {
        // TODO: Implement exec() method.
    }

    function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        // TODO: Implement select() method.
    }

    function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): object|false
    {
        // TODO: Implement selectOne() method.
    }

    function insert(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        // TODO: Implement insert() method.
    }

    function update(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        // TODO: Implement update() method.
    }

    function delete(string $tableName, string $condition, array $bindings = []): int
    {
        // TODO: Implement delete() method.
    }
}
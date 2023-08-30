<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Contracts\DatabaseConnectionInterface;
use PDO;

class MySqlConnection extends PDO implements DatabaseConnectionInterface
{

    function exec(string $query, array $bindings = []): int
    {
        try {
            $stmt = $this->prepare($query);
            $stmt->execute($bindings);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            // Handle error, possibly re-throw or log
            return 0;
        }
    }

    function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $cols = implode(',', $columns);
        $query = "SELECT $cols FROM $tableName" . ($condition ? " WHERE $condition" : '');
        $this->exec($query, $bindings);
        $stmt = $this->prepare($query);
        $stmt->execute($bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): object|false
    {
        $cols = implode(',', $columns);
        $query = "SELECT $cols FROM $tableName" . ($condition ? " WHERE $condition LIMIT 1" : ' LIMIT 1');
        $this->exec($query, $bindings);
        $stmt = $this->prepare($query);
        $stmt->execute($bindings);
        return (object) $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function insert(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        $cols = implode(',', array_keys($values));
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $query = "INSERT INTO $tableName ($cols) VALUES ($placeholders)" . ($condition ? " WHERE $condition" : '');
        return $this->exec($query, array_values($values) + $bindings);
    }

    function update(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        $set = implode(',', array_map(fn($k) => "$k = ?", array_keys($values)));
        $query = "UPDATE $tableName SET $set" . ($condition ? " WHERE $condition" : '');
        return $this->exec($query, array_values($values) + $bindings);
    }

    function delete(string $tableName, string $condition, array $bindings = []): int
    {
        $query = "DELETE FROM $tableName WHERE $condition";
        return $this->exec($query, $bindings);
    }
}

<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Contracts\DatabaseConnectionInterface;
use PDO;
use PDOStatement;
use ReturnTypeWillChange;

class MySqlConnection extends PDO implements DatabaseConnectionInterface
{

    #[ReturnTypeWillChange]
    public function exec(string $query, array $bindings = []): object
    {
        $stmt = $this->prepare($query);
        $stmt->execute($bindings);

        SqlDebugger::logQuery($stmt->queryString);

        return $stmt;
    }

    public function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $cols = implode(',', $columns);
        $query = "SELECT $cols FROM $tableName" . ($condition !== null ? " WHERE $condition" : '');

        return $this->exec($query, $bindings)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $cols = implode(',', $columns);
        $query = "SELECT $cols FROM $tableName" . ($condition !== null ? " WHERE $condition LIMIT 1" : ' LIMIT 1');

        return $this->exec($query, $bindings)->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        $cols = implode(',', array_keys($values));

        $stringValues = $this->prepareValuesForInsert($values);
        $query = "INSERT INTO $tableName ($cols) VALUES ($stringValues)" . ($condition !== null ? " WHERE $condition" : '');

        return $this->exec($query, $bindings);
    }

    public function update(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        $set =  $this->prepareValuesForUpdate($values);

        $query = "UPDATE $tableName SET $set" . ($condition !== null ? " WHERE $condition" : '');

        return $this->exec($query, $bindings)->rowCount();
    }

    public function delete(string $tableName, string $condition, array $bindings = []): int
    {
        $query = "DELETE FROM $tableName WHERE $condition";

        return $this->exec($query, $bindings)->rowCount();
    }

    private function prepareValuesForInsert(array $values) : string
    {
        $result = [];

        foreach ($values as $item) {
            if (is_string($item)) {
                $result[] = "'" . $item . "'";
                continue;
            }

            $result[] = $item;
        }
        $string = implode(',', $result);

        return $string;
    }

    private function prepareValuesForUpdate(array $values) : string
    {
        $result = [];

        foreach ($values as $column => $item) {

            if (is_string($item)) {
                $result[] = $column . ' = ' . "'" . $item . "'";
                continue;
            }

            $result[] = $column . ' = ' .$item;
        }
        $string = implode(',', $result);

        return $string;
    }
}
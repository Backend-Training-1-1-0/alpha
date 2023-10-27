<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Components\DIContainer\DIContainer;
use Alpha\Components\EventDispatcher\DatabaseEvent;
use Alpha\Components\EventDispatcher\Message;
use Alpha\Contracts\DatabaseConnectionInterface;
use Alpha\Contracts\EventDispatcherInterface;
use Alpha\Contracts\ObserverInterface;
use PDO;
use PDOStatement;

class MySqlConnection extends PDO implements DatabaseConnectionInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        string                           $dsn,
        ?string                          $username = null,
        ?string                          $password = null,
        ?array                           $options = null
    )
    {
        parent::__construct($dsn, $username, $password, $options);
    }

    public function exec(string $query, array $bindings = []): int
    {
        $stmt = $this->prepare($query);
        $stmt->execute($bindings);

        $this->triggerLogEvent($stmt);

        return $stmt->rowCount();
    }

    public function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $cols = implode(',', $columns);
        $query = "SELECT $cols FROM $tableName" . ($condition !== null ? " WHERE $condition" : '');
        $stmt = $this->prepare($query);
        $stmt->execute($bindings);

        $this->triggerLogEvent($stmt);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $cols = implode(',', $columns);
        $query = "SELECT $cols FROM $tableName" . ($condition !== null ? " WHERE $condition LIMIT 1" : ' LIMIT 1');
        $stmt = $this->prepare($query);
        $stmt->execute($bindings);

        $this->triggerLogEvent($stmt);

        return $stmt->fetch(PDO::FETCH_ASSOC);
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
        $set = $this->prepareValuesForUpdate($values);

        $query = "UPDATE $tableName SET $set" . ($condition !== null ? " WHERE $condition" : '');

        return $this->exec($query, $bindings);
    }

    public function delete(string $tableName, string $condition, array $bindings = []): int
    {
        $query = "DELETE FROM $tableName WHERE $condition";

        return $this->exec($query, $bindings);
    }

    private function prepareValuesForInsert(array $values): string
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

    private function prepareValuesForUpdate(array $values): string
    {
        $result = [];

        foreach ($values as $column => $item) {

            if (is_string($item)) {
                $result[] = $column . ' = ' . "'" . $item . "'";
                continue;
            }

            $result[] = $column . ' = ' . $item;
        }
        $string = implode(',', $result);

        return $string;
    }

    private function triggerLogEvent(PDOStatement $stmt): void
    {
        $this->eventDispatcher->notify(DatabaseEvent::SQL_CREATED, new Message($stmt->queryString));
    }
}
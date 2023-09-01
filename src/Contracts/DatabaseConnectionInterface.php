<?php

namespace Alpha\Contracts;

interface DatabaseConnectionInterface
{
    function exec(string $query, array $bindings = []): int;

    function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false;

    function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false;

    function insert(string $tableName, array $values, string $condition = null, array $bindings = []): int;

    function update(string $tableName, array $values, string $condition = null, array $bindings = []): int;

    function delete(string $tableName, string $condition, array $bindings = []): int;
}
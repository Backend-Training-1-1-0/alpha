<?php

namespace Alpha\Components\EntityManager;

interface EntityManagerInterface
{
    function find(string $entityClass, int $id): ?object;

    public function findByCondition(string $entityClass, string $condition, array $bindings): ?object;

    public function save(object $entityClass): bool;
}
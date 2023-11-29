<?php

namespace Alpha\Components\EntityManager;

interface EntityManagerInterface
{
    function find(string $entityClass, int $id): object;

    function persist(string $entityClass): void;
}
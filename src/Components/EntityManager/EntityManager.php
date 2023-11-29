<?php

namespace Alpha\Components\EntityManager;

use Alpha\Components\Attributes\Column;
use Alpha\Components\Attributes\Table;
use Alpha\Components\DatabaseConnection\MySqlConnection;
use Alpha\Components\EntityManager\EntityManagerInterface;
use Alpha\Contracts\DatabaseConnectionInterface;
use PDO;
use ReflectionClass;

class EntityManager
{
    public function __construct(private DatabaseConnectionInterface $db)
    {
    }

    public function find(string $entityClass, int $id): ?object
    {
        $tableName = $this->getTableName($entityClass);

        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            return $this->createEntity($entityClass, $data);
        }

        return null;
    }

    public function persist(string $entityClass): void
    {
        $tableName = $this->getTableName($entityClass);

        // Assume there's a getId() method on the entity
        $id = $entity->getId();

        if ($id) {
            // Update existing entity
            // Implement update logic here
        } else {
            // Insert new entity
            // Implement insert logic here
        }
    }

    private function createEntity($entityClass, $data)
    {
        $entity = new $entityClass();

        foreach ($data as $property => $value) {
            // Assume there are corresponding setter methods on the entity
            $setterMethod = 'set' . ucfirst($property);
            if (method_exists($entity, $setterMethod)) {
                $entity->$setterMethod($value);
            }
        }

        return $entity;
    }

    public function getTableName($entityClass)
    {
        $reflectionClass = new ReflectionClass($entityClass);

        /** @var Table $table */
        $table = ($reflectionClass->getAttributes(Table::class)[0])->newInstance();

        return $table->tableName;
    }

    public function getColumns($entityClass)
    {
        $properties = (new ReflectionClass($entityClass))->getProperties();

        foreach ($properties as $property) {
            dump($property->setValue());
            dump($property);
            foreach ($property->getAttributes() as $attribute) {
                dd($attribute->newInstance());
            }
        }
    }
}
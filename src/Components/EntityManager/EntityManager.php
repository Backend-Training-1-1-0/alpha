<?php

namespace Alpha\Components\EntityManager;

use Alpha\Components\Attributes\Column;
use Alpha\Components\Attributes\Table;
use Alpha\Contracts\DatabaseConnectionInterface;
use DateTime;
use PDO;
use ReflectionClass;
use ReflectionException;

class EntityManager implements EntityManagerInterface
{
    public function __construct(private readonly DatabaseConnectionInterface $db) { }

    /**
     * Получение сущности по id
     * @param string $entityClass
     * @param int $id
     * @return object|null
     * @throws ReflectionException
     */
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

    /**
     * Получение сущности по условию
     * @param string $entityClass
     * @param string $condition
     * @param array $bindings
     * @return object|null
     * @throws ReflectionException
     */
    public function findByCondition(string $entityClass, string $condition, array $bindings): ?object
    {
        $tableName = $this->getTableName($entityClass);

        $data = $this->db->selectOne($tableName, $this->getEntityColumns($entityClass), $condition, $bindings);

        if ($data) {
            return $this->createEntity($entityClass, $data);
        }

        return null;
    }

    /**
     * Сохранение или обновление сущности
     * @param object $entityClass
     * @return bool
     */
    public function save(object $entityClass): bool
    {
        $tableName = $this->getTableName($entityClass);
        $values = $this->getEntityValues($entityClass);

        if (empty($values['id'])) {
            $this->db->insert($tableName, $values);
            return true;
        }

        $this->db->update($tableName, $values, 'id = :id', [':id' => $values['id']]);
        return true;
    }

    /**
     * Создание сущности
     * @param string $entityClass
     * @param array $data
     * @return object
     * @throws ReflectionException
     */
    private function createEntity(string $entityClass, array $data): object
    {
        $entity = new $entityClass();

        $this->mapData($entityClass, $entity, array_keys($data), $data);

        return $entity;
    }

    /**
     * Получение названия таблицы
     * @param $entityClass
     * @return string
     */
    public function getTableName($entityClass): string
    {
        $reflectionClass = new ReflectionClass($entityClass);

        /** @var Table $table */
        $table = ($reflectionClass->getAttributes(Table::class)[0])->newInstance();

        return $table->tableName;
    }

    /**
     * @param string $entityClass
     * @param object|null $entity
     * @param array|null $columnNames
     * @param array $columnValues
     * @return void
     * @throws ReflectionException
     */
    private function mapData(
        string $entityClass,
        object $entity = null,
        array $columnNames = null,
        array $columnValues = [],
    ): void
    {
        $properties = (new ReflectionClass($entityClass))->getProperties();

        foreach ($properties as $property) {
            foreach ($property->getAttributes(Column::class) as $attribute) {
                /** @var Column $column */
                $column = $attribute->newInstance();

                if (in_array($column->name, $columnNames)) {

                    if (DateTime::createFromFormat('Y-m-d H:i:s', $columnValues[$column->name]) !== false) {
                        $property->setValue(
                            $entity,
                            DateTime::createFromFormat('Y-m-d H:i:s', $columnValues[$column->name])
                        );

                        continue;
                    }

                    $property->setValue($entity, $columnValues[$column->name]);
                }
            }
        }
    }

    /**
     * @param object $entityClass
     * @return array
     */
    private function getEntityValues(object $entityClass): array
    {
        $properties = (new ReflectionClass($entityClass))->getProperties();
        $values = [];

        foreach ($properties as $property) {
            foreach ($property->getAttributes(Column::class) as $attribute) {
                if ($property->isInitialized($entityClass) === false) {
                    continue;
                }

                $value = $property->getValue($entityClass);
                /** @var Column $column */
                $column = $attribute->newInstance();

                if ($value instanceof DateTime) {
                    $values[$column->name] = $value->format('Y-m-d H:i:s');
                    continue;
                }

                $values[$column->name] = $value;
            }
        }

        return $values;
    }

    /**
     * @param string $entityClassName
     * @return array
     * @throws ReflectionException
     */
    private function getEntityColumns(string $entityClassName): array
    {
        $properties = (new ReflectionClass($entityClassName))->getProperties();
        $columns = [];

        foreach ($properties as $property) {
            foreach ($property->getAttributes(Column::class) as $attribute) {
                $columns[] = ($attribute->newInstance())->name;
            }
        }

        return $columns;
    }
}
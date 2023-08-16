<?php

namespace Alpha\Components\DIContainer;

use Alpha\Contracts\DIContainerInterface;
use ReflectionFunction;
use ReflectionMethod;
use RuntimeException;

include 'functions.php';

class DIContainer implements DIContainerInterface
{
    private array $container = [];
    private static self $instance;

    private function __construct(private array $config) { }

    public function __clone(): void
    {
        throw new RuntimeException('Клонирование запрещено!');
    }

    public static function getInstance(array $config = []): self
    {
        if (empty(self::$instance)) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    public function build(string $className): object
    {
        $constructor = new ReflectionMethod($className, '__construct');

        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependenceType = (string) $parameter->getType();

            if (isset($this->container[$dependenceType])) {
                $dependencies[] = $this->container[$dependenceType];

                continue;
            }

            $dependencies[] = $this->build($this->config[$dependenceType]);
        }

        return new $className(...$dependencies);
    }

    public function make(string $interfaceName): object
    {
        if (isset($this->config[$interfaceName]) === false) {
            throw new \OutOfRangeException('Нет такого интерфейса в config');
        }

        $instance = $this->container[$interfaceName] ?? $this->build($this->config[$interfaceName]);
        $this->register($interfaceName, $instance);

        return $instance;
    }

    public function register(string $contract, object $dependence): void
    {
        $this->container[$contract] = $dependence;
    }

    public function call(string|callable $handler, string|null $method = null, array $params = []): mixed
    {
        if (is_string($handler) && empty($method) === true) {
            throw new \InvalidArgumentException("При вызове метода класса {$handler}, необходимо передать имя метода");
        }

        $reflection = is_callable($handler) ? new ReflectionFunction($handler) : new ReflectionMethod($handler, $method);

        $parameters = $reflection->getParameters();

        $arguments = [];

        foreach ($parameters as $parameter) {
            $typeDependency = $parameter->getType();

            if ((empty($typeDependency) === false && $typeDependency->isBuiltin() === true) || empty($typeDependency) === true) {
                $paramExists = in_array($parameter->getName(), array_keys($params));

                if ($typeDependency->allowsNull() === false && $paramExists === false) {
                    throw new \InvalidArgumentException('отсутствуют обязательные аргументы: ' . $parameter->getName());
                }

                if ($paramExists === true) {
                    $arguments[] = $params[$parameter->getName()];
                }

                if ($parameter->isDefaultValueAvailable()=== true && $paramExists === false) {
                    $arguments[] = $parameter->getDefaultValue();
                }
            }

            if ((empty($typeDependency) === false && $typeDependency->isBuiltin() === false)) {
                $arguments[] = $this->build($this->config[$typeDependency->getName()]);
            }
        }

        if (is_callable($handler)) {
            return $handler(...$arguments);
        }

        return $this->build($handler)->{$method}(...$arguments);
    }
}

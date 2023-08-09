<?php

namespace Components\DIContainer;

use Contracts\DIContainerInterface;
use http\Exception\RuntimeException;
use ReflectionMethod;

include 'functions.php';

class DIContainer implements DIContainerInterface
{
    private array $container = [];
    private static self $instance;

    private function __construct(private array $config) { }

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

        $dependences = [];

        foreach ($parameters as $parameter) {
            $dependenceType = (string) $parameter->getType();

            if (isset($this->container[$dependenceType])) {
                $dependences[] = $this->container[$dependenceType];

                continue;
            }

            $dependences[] = $this->build($this->config[$dependenceType]);
        }

        return new $className(...$dependences);
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

    public function __clone(): void
    {
        throw new RuntimeException('Клонирование запрещено!');
    }
}

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

    private function __construct(private array $config)
    {
    }

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
            $dependenceType = (string)$parameter->getType();

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
            throw new \OutOfRangeException('Нет ' . $interfaceName . ' в config');
        }

        $instance = $this->container[$interfaceName] ?? $this->build($this->config[$interfaceName]);
        $this->singleton($interfaceName, $instance);

        return $instance;
    }

    public function singleton(string $contract, string|callable|object $dependency): void
    {
        if (is_string($dependency)) {
            $instance = new $dependency;
        }

        if (is_callable($dependency)) {
            $dependency = $dependency($this);
        }

        $this->config[$contract] = $dependency::class;
        $this->container[$contract] = $dependency;
    }

    public function call(string|callable|object $handler, string|null $method = null, array $defaultArgs = []): mixed
    {
        if (is_string($handler) && empty($method) === true) {
            throw new \InvalidArgumentException("При вызове метода класса {$handler}, необходимо передать имя метода");
        }

        $reflection = is_callable($handler) ? new ReflectionFunction($handler) : new ReflectionMethod($handler, $method);

        $parameters = $reflection->getParameters();

        $arguments = [];

        foreach ($parameters as $parameter) {
            if (empty($parameter->getType()) === true) {
                continue;
            }

            if ($parameter->getType()->isBuiltin() === true) {
                continue;
            }

            $arguments[] = $this->build($this->config[$parameter->getType()->getName()]);
        }

        $arguments = array_merge($defaultArgs, $arguments);

        if (is_callable($handler)) {
            return $handler(...$arguments);
        }

        if (is_object($handler)) {
            $handler->{$method}(...$arguments);
        }

        return $this->build($handler)->{$method}(...$arguments);
    }
}

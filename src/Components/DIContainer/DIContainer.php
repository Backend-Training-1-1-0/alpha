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
        if (method_exists($className, '__construct') === false) {
            return new $className();
        }

        $constructor = new ReflectionMethod($className, '__construct');

        $parameters = $constructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependenceType = $parameter->getType()->getName();

            if ($parameter->getType()->isBuiltin() === true) {
                continue;
            }

            if (isset($this->container[$dependenceType])) {
                $dependency = $this->container[$dependenceType];

                if (is_callable($dependency)) {
                    $dependencies[] = $dependency($this);
                    continue;
                }

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
            $dependency = new $dependency;
        }

        if (is_callable($dependency)) {
            $this->config[$contract] = $dependency;
            $this->container[$contract] = $dependency;
            return;
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

            $argument = $this->config[$parameter->getType()->getName()];

            if (is_callable($argument)) {
                $arguments[] = $argument($this);

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

    public function get(string $contract)
    {
        if (isset($this->container[$contract])) {
            $dependency = $this->container[$contract];

            if (is_callable($dependency)) {
                $this->container[$contract] = $dependency($this);
                return $this->container[$contract];
            }

            return $dependency;
        }

        throw new \Exception("Зависимость для контракта не найдена: $contract");
    }
}

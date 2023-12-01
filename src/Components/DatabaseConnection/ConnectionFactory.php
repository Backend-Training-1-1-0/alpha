<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Contracts\DatabaseConnectionInterface;
use Alpha\Contracts\EventDispatcherInterface;
use Exception;

class ConnectionFactory
{
    private static EventDispatcherInterface $eventDispatcher;
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        self::$eventDispatcher = $eventDispatcher;
    }

    public static function make(array $config): DatabaseConnectionInterface
    {
        $driver = $config['driver'];

        return match ($driver) {
            'mysql' => (new MariaDBConnector(self::$eventDispatcher))->connect($config),
            default => throw new Exception("Неизвестный драйвер: $driver"),
        };
    }
}
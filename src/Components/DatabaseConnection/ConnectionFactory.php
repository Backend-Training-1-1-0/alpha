<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Contracts\DatabaseConnectionInterface;
use Exception;

include 'functions.php';

class ConnectionFactory
{
    public static function make(array $config): DatabaseConnectionInterface
    {
        $driver = $config['driver'];

        //TODO: заменить на match
        switch ($driver) {
            case 'mysql':
                return (new MariaDBConnector())->connect($config);
            default:
                throw new Exception("Неизвестный драйвер: $driver");
        }
    }
}
<?php

namespace Alpha\Components\DatabaseConnection;

use Exception;
use PDO;

class СonnectionFactory
{
    public static function make(array $config): PDO
    {
        $driver = $config['driver'];

        switch ($driver) {
            case 'mysql':
                return (new MariaDBConnector())->connect($config);
            default:
                throw new Exception("Неизвестный драйвер: $driver");
        }
    }
}
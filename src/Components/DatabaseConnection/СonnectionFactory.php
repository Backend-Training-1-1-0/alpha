<?php

namespace Alpha\Components\DatabaseConnection;

use Exception;

class СonnectionFactory
{
    public static function createConnection($config)
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
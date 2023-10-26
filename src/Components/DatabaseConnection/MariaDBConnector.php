<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Contracts\EventDispatcherInterface;
use Exception;
use PDO;
use PDOException;

class MariaDBConnector
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public function connect(array $config): MySqlConnection
    {
        try {
            $dsn = "mysql:host={$config['host']};port=3306;"
                . "dbname={$config['database']};charset={$config['charset']}";

            return new MySqlConnection(
                eventDispatcher: $this->eventDispatcher,
                dsn: $dsn,
                username: $config['username'],
                password: $config['password'],
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
        } catch (PDOException $e) {
            throw new Exception("Ошибка при соединении с БД: " . $e->getMessage());
        }
    }
}
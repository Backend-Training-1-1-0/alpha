<?php

namespace Alpha\Components\DatabaseConnection;

use Exception;
use PDO;
use PDOException;

class MariaDBConnector
{
    public function connect(array $config): PDO
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};"
                . "dbname={$config['database']};charset={$config['charset']}";

            return new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Ошибка при соединении с БД: " . $e->getMessage());
        }
    }
}
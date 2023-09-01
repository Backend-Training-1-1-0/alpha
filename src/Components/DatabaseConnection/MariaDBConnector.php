<?php

namespace Alpha\Components\DatabaseConnection;

use Exception;
use PDO;
use PDOException;

class MariaDBConnector
{
    public function connect(array $config): MySqlConnection
    {
        try {
            $dsn = "mysql:host={$config['host']};port=3306;"
                . "dbname={$config['database']};charset={$config['charset']}";

            return new MySqlConnection($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Ошибка при соединении с БД: " . $e->getMessage());
        }
    }
}
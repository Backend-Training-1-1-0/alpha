<?php

namespace Alpha\Components\DatabaseConnection;

class SqlDebugger
{
    public static function logQuery(string $query): void
    {
        if (getenv('MYSQL_LOG') !== 'true') {
            return;
        }

        file_put_contents(getenv('MYSQL_PATH_LOG'), $query . PHP_EOL, FILE_APPEND);
    }
}
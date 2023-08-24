<?php

namespace Alpha\Console;

use Alpha\Contracts\ConsoleOutputInterface;

class ConsoleOutput implements ConsoleOutputInterface
{
    public function __construct() { }

    public function stdout(string $result): void
    {
        echo $result;
    }

    public function info(string $result): void
    {
        $this->stdout("\033[34m" . $result . "\033[0m");
    }

    public function warning(string $result): void
    {
        $this->stdout("\033[38;5;214m" . $result . "\033[0m");
    }

    public function success(string $result): void
    {
        $this->stdout("\033[32m" . $result . "\033[0m");
    }

}

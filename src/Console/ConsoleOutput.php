<?php

namespace Alpha\Console;

use Alpha\Contracts\ConsoleOutputInterface;

class ConsoleOutput implements ConsoleOutputInterface
{
    public function __construct() { }

    public function stdout(string $result, $mode = ''): void
    {
        if ($mode === 'success') {
            $result = "\033[32m" . $result . "\033[0m";
        }

        if ($mode === 'info') {
            $result = "\033[34m" . $result . "\033[0m";
        }

        if ($mode === 'warning') {
            $result = "\033[38;5;214m" . $result . "\033[0m";
        }

        echo $result;
    }

    public function info(string $result): void
    {
        $this->stdout($result, 'info');
    }

    public function warning(string $result): void
    {
        $this->stdout($result, 'warning');
    }

    public function success(string $result): void
    {
        $this->stdout($result, 'success');
    }

}

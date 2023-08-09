<?php

namespace Alpha\Components\ConsoleOutput;

use Alpha\Contracts\ConsoleOutputInterface;

class ConsoleOutput implements ConsoleOutputInterface
{
    public function __construct() { }

    public function stdout(string $result): void
    {
        echo $result;
    }
}

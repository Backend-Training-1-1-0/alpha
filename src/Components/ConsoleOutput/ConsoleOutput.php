<?php

namespace Components\ConsoleOutput;

use Contracts\ConsoleOutputInterface;

class ConsoleOutput implements ConsoleOutputInterface
{
    public function __construct() { }

    public function stdout(string $result): void
    {
        echo $result;
    }
}

<?php

namespace Components\ConsoleOutput;

class ConsoleOutput implements ConsoleOutputInterface
{
    public function __construct() { }

    public function stdout(string $result): void
    {
        echo $result;
    }
}

<?php

namespace Components\ConsoleOutput;

interface ConsoleOutputInterface
{
     function __construct();
     function stdout(string $result): void;
}

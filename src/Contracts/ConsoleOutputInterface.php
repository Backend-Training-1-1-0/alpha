<?php

namespace Alpha\Contracts;

interface ConsoleOutputInterface
{
     function stdout(string $result): void;
     function warning(string $result): void;
     function success(string $result): void;
     function info(string $result): void;
}

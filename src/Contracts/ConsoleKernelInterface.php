<?php

namespace Alpha\Contracts;

use Alpha\Components\ConsoleInput\ConsoleInput;

interface ConsoleKernelInterface
{
    function __construct();
    function handle(array $argv): void;
    function addCommandsNamespaces(array $commandsNamespaces): void;
    function addCommand(string $className): void;
    function dispatch(array $argv): void;
    function getCommandMap();
    function addArgumentsInInput(ConsoleInput $input, array $argv, string $paramString): void;
}

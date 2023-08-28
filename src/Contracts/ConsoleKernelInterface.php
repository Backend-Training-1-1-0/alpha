<?php

namespace Alpha\Contracts;

interface ConsoleKernelInterface
{
    function handle(array $argv): void;
    function addCommandsNamespaces(array $commandsNamespaces): void;
    function addCommand(string $className): void;
    function dispatch(array $argv): void;
    function getCommandMap(): array;
    function terminate(): void;
}

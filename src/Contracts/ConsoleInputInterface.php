<?php

namespace Alpha\Contracts;

use Alpha\Console\CommandDefinition;

interface ConsoleInputInterface
{
    function bindDefinition(ConsoleCommandInterface $command): void;
    function hasOption(string $option): bool;
    function getArgument(string $argument): mixed;
    function hasArgument(string $argument): bool;
    function addPlugins(array $plugins): void;
    function getDefinition(): CommandDefinition;
}

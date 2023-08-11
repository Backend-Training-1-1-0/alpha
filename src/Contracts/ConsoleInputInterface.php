<?php

namespace Alpha\Contracts;

interface ConsoleInputInterface
{
    function __construct();
    function bindDefinition(ConsoleCommandInterface $command): void;
    function hasOption(string $option): bool;
    function getArgument(string $argument): mixed;
    function hasArgument(string $argument): bool;
}

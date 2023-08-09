<?php

namespace Alpha\Contracts;

interface ConsoleInputInterface
{
    function __construct();
    function bindDefinition(ConsoleCommandInterface $command): void;
    function getOption(string $option): mixed;
    function hasOption(string $option): bool;
    function getArgument(string $option): mixed;
    function hasArgument(string $argument): bool;
}

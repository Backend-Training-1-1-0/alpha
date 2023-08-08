<?php

namespace Components\ConsoleInput;

interface ConsoleInputInterface
{
    function __construct();
    function getOptions(): array;
    function setOptions(array $options): void;
    function getOption(string $option): mixed;
    function hasOption(string $option): bool;
    function getArguments(): array;
    function setArguments(array $arguments): void;
    function getArgument(string $argument): mixed;
    function hasArgument(string $argument): bool;
    function removeArgumentStartingWithDoubleDash(): void;
}

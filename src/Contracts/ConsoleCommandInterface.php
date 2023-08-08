<?php

namespace Contracts;

interface ConsoleCommandInterface
{
    static function getName(): string;

    static function getDescription(): string;

    function getCommandInfo(array $args): void;

    function execute(): void;
    function getArguments(): array;

    static function isHidden(): bool;
}

<?php

namespace Alpha\Contracts;

interface ConsoleCommandInterface
{
    static function getSignature(): string;
    static function getDescription(): string;
    function getHidden(): bool;
    function execute(): void;
}

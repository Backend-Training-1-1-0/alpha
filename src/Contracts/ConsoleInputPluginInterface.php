<?php

namespace Alpha\Contracts;

interface ConsoleInputPluginInterface
{
    function isSuitable(ConsoleInputInterface $input): bool;
    function handle(ConsoleInputInterface $input): void;
}
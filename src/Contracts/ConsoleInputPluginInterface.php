<?php

namespace Alpha\Contracts;

interface ConsoleInputPluginInterface
{
    function define(ConsoleInputInterface $input): void;
    function isSuitable(ConsoleInputInterface $input): bool;
    function handle(ConsoleInputInterface $input): void;
}
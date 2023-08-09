<?php

namespace Alpha\Contracts;

interface ConsoleRequestInterface
{
    function server(): array;

    function get(): array;

    function post(): array;
}

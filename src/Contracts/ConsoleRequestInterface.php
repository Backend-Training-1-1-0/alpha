<?php

namespace Contracts;

interface ConsoleRequestInterface
{
    function server(): array;

    function get(): array;

    function post(): array;
}

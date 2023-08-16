<?php

namespace Alpha\Contracts;

interface ConsoleRouterInterface
{
    function dispatch(array $argv): void;
}

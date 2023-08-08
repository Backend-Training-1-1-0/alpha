<?php

namespace Contracts;

interface ConsoleRouterInterface
{
    function __construct();

    function dispatch(array $argv): void;
}

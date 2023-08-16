<?php

namespace Alpha\Contracts;

interface HttpRequestInterface
{
    function server(): array;
    function post(): array;
    function get(): array;
}

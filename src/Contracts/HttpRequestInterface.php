<?php

namespace Contracts;

interface HttpRequestInterface
{
    function __construct();
    function server(): array;
    function post(): array;
    function get(): array;
}
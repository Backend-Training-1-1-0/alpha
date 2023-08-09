<?php

namespace FHttp;

use Contracts\HttpRequestInterface;

class Request implements HttpRequestInterface
{
    public function __construct() { }

    public function server(): array
    {
        return $_SERVER;
    }

    public function post(): array
    {
        return $_POST;
    }

    public function get(): array
    {
        return $_GET;
    }
}

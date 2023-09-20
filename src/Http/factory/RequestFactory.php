<?php

namespace Alpha\Http\factory;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Alpha\Http\Request;

class RequestFactory implements RequestFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request(method: $method, uri: $uri);
    }
}
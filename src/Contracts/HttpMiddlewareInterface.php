<?php

namespace Alpha\Contracts;

use Alpha\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface HttpMiddlewareInterface
{
    public function execute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ?ResponseInterface;
}

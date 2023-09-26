<?php

namespace Alpha\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface HttpRouterInterface
{
    function dispatch(ServerRequestInterface $request): mixed;
    function addMiddleware(callable|string|array $middleware);
    function add(string $method, string $route, string|callable $handler, callable|string|array $middleware = []): void;
    function group(string $prefix, callable $groupCallback, callable|string|array $middleware = []): void;
    function get(string $route, string|callable $handler, callable|string|array $middlewares = []): void;
    function post(string $route, string|callable $handler, callable|string|array $middlewares = []): void;
}

<?php

namespace Http;

use Framework\Contracts\{
    HttpMiddlewareInterface,
    HttpRequestInterface,
    HttpRouterInterface};
use InvalidArgumentException;

class Router implements HttpRouterInterface
{
    private array $routes = [];

    private array $middlewares = [];

    private array $groupMiddlewares = [];

    private array $groupStack = [];

    public function __construct()
    {
    }

    public function dispatch(HttpRequestInterface $request): mixed
    {
        $method = $request->server()['REQUEST_METHOD'];

        $path = parse_url($request->server()['REQUEST_URI'], PHP_URL_PATH);

        if (isset($this->routes[$method][$path]) === false) {
            throw new \RuntimeException('Путь не найден', 404);
        }

        /** @var Route $route */
        foreach ($this->routes[$method][$path] as $route) {
            $handler = $route->handler;

            $arguments = $this->mapArgs($request, $route);

            // Вызов глобального middleware
            $this->handleMiddleware($this->middlewares, $request);

            // Вызов middleware маршрута
            $this->handleMiddleware($route->middleware, $request);

            //вызов middleware группы
            foreach ($route->groupStack as $group) {
                $this->handleMiddleware($this->groupMiddlewares[$group], $request);
            }
            $arguments[] = $request;

            if (is_callable($handler)) {
                return $handler(...$arguments);
            }

            $handlerClass = container()->build($route->handler);

            return $handlerClass->{$route->action}(...$arguments);
        }
    }

    public function addMiddleware(callable|string|array $middleware)
    {
        if (is_array($middleware)) {
            $this->middlewares = array_merge($this->middlewares, $middleware);
            return;
        }

        $this->middlewares[] = $middleware;
    }

    private function resolveHandler(string|callable $handler): array
    {
        if (is_callable($handler) === true) {
            return [
                $handler,
                null
            ];
        }

        $handlerPair = explode('::', $handler);

        if (count($handlerPair) != 2) {
            throw new InvalidArgumentException('Неизвестная аннотация обработчика');
        }

        return [
            $handlerPair[0],
            $handlerPair[1]
        ];
    }

    public function add(string $method, string $route, string|callable $handler, callable|string|array $middleware = []): void
    {
        if(is_array($middleware) === false) {
            $middleware = [$middleware];
        }

        $path = parse_url($route, PHP_URL_PATH);

        if (empty($this->groupStack) === false) {
            $path = '/' . implode('/', $this->groupStack) . $path;
        }

        [$handler, $action] = $this->resolveHandler($handler);

        $this->routes[$method][$path][] = new Route(
            $path,
            $method,
            $handler,
            $this->prepareParams($route),
            $action,
            $middleware,
            $this->groupStack
        );
    }

    public function group(string $prefix, callable $groupCallback, callable|string|array $middleware = []): void
    {
        $this->groupStack[] = $prefix;

        $groupCallback($this);

        if(is_array($middleware) === false) {
            $middleware = [$middleware];
        }

        $this->groupMiddlewares[$prefix] = $middleware;

        array_pop($this->groupStack);
    }

    public function get(string $route, string|callable $handler, callable|string|array $middlewares = []): void
    {
        $this->add('GET', $route, $handler, $middlewares);
    }

    public function post(string $route, string|callable $handler, callable|string|array $middlewares = []): void
    {
        $this->add('POST', $route, $handler, $middlewares);
    }

    private function handleMiddleware(array $middlewares, HttpRequestInterface $request): void
    {
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                $middleware($request);
                continue;
            }

            if (is_string($middleware) === false) {
                throw new InvalidArgumentException();
            }

            $middlewareInstance = new $middleware;

            if ($middlewareInstance instanceof HttpMiddlewareInterface === false) {
                throw new InvalidArgumentException();
            }

            $middlewareInstance->execute($request);
        }
    }

    private function mapArgs(HttpRequestInterface $request, Route $route): array
    {
        $arguments = [];

        foreach ($route->params as $param) {
            $paramExists = in_array($param['name'], array_keys($request->get()));

            if ($param['isRequired'] === true && $paramExists === false) {
                throw new \InvalidArgumentException('отсутствуют обязательные аргументы: ' . $param['name']);
            }

            if ($paramExists === true) {
                $arguments[] = $request->get()[$param['name']];
            }

            if (empty($paramProperties['defaultValue']) === false && $paramExists === false) {
                $arguments[] = $paramProperties['defaultValue'];
            }
        }

        return $arguments;
    }

    private function prepareParams(string $route): array
    {
        $paramString = parse_url($route, PHP_URL_QUERY) ?? "";

        $resultParams = [];
        preg_match_all('/\{([^}]*)\}/', $paramString, $params);

        foreach ($params[1] as $param) {
            $isRequired = true;
            if (str_starts_with($param, '?')) {
                $param = substr($param, 1);
                $isRequired = false;
            }

            $defaultValue = null;
            if (substr_count($param, '=') === 1 && str_ends_with($param, '=') === false) {
                $arr = explode('=', $param);
                $defaultValue = $arr[1];
                $param = $arr[0];
            }

            $resultParams[] = [
                'name' => $param,
                'isRequired' => $isRequired,
                'defaultValue' => $defaultValue,
            ];
        }

        return $resultParams;
    }
}

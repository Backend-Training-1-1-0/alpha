<?php

namespace Alpha\Http;

use Alpha\Http\factory\MiddlewareFactory;
use Alpha\Contracts\{
    HttpRouterInterface,
};
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements HttpRouterInterface
{
    private array $routes = [];

    private array $middlewares = [];

    private array $groupMiddlewares = [];

    private array $groupStack = [];

    public function __construct(
        private readonly MiddlewareFactory $middlewareFactory,
    ) { }

    public function dispatch(ServerRequestInterface $request): mixed
    {
        $method = $request->getServerParams()['REQUEST_METHOD'];

        $path = parse_url($request->getServerParams()['REQUEST_URI'], PHP_URL_PATH);

        if (isset($this->routes[$method][$path]) === false) {

            $notFoundRoute = true;
            foreach ($this->routes[$method] as $uri => $route) {
                $pattern = preg_replace('#\{[^}]+\}#', '(\d+)', $uri);
                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $path, $matches)) {
                    $this->routes[$method][$path] = $route;
                    unset($this->routes[$method][$uri]);

                    $notFoundRoute = false;
                    break;
                }
            }

            if ($notFoundRoute) {
                throw new \RuntimeException('Путь не найден', 404);
            }
        }

        //TODO: получать из DI по интерфейсу
        $response = new Response();

        /** @var Route $route */
        foreach ($this->routes[$method][$path] as $route) {
            $handler = $route->handler;

            // Вызов глобального middleware
            $response = $this->handleMiddleware($this->middlewares, $request, $response);

            // Вызов middleware маршрута
            $response = $this->handleMiddleware($route->middleware, $request, $response);

            //вызов middleware группы
            foreach ($route->groupStack as $group) {
                $response = $this->handleMiddleware($this->groupMiddlewares[$group], $request, $response);
            }

            //костыль, выпилить как то
            if ($response->getStatusCode() !== 200) {
                return $response;
            }

            $defaultArguments = $this->mapArgs($request, $route);

            return container()->call($handler, $route->action, $defaultArguments);
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
        if (is_array($middleware) === false) {
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

        if (is_array($middleware) === false) {
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

    public function put(string $route, string|callable $handler, callable|string|array $middlewares = []): void
    {
        $this->add('PUT', $route, $handler, $middlewares);
    }

    public function patch(string $route, string|callable $handler, callable|string|array $middlewares = []): void
    {
        $this->add('PATCH', $route, $handler, $middlewares);
    }

    public function delete(string $route, string|callable $handler, callable|string|array $middlewares = []): void
    {
        $this->add('DELETE', $route, $handler, $middlewares);
    }

    private function handleMiddleware(
        array $middlewares,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {

        $middlewareChain = $this->middlewareFactory->createMiddlewareChain($middlewares);

        return $middlewareChain($request, $response);
    }

    private function mapArgs(ServerRequestInterface $request, Route $route): array
    {
        $arguments = [];

        /** @var RouteParameter $param */
        foreach ($route->params as $param) {
            $paramExists = in_array($param->name, array_keys($request->getQueryParams()));

            if ($param->isRequired === true && $paramExists === false) {
                $arguments = $this->mapArgsFromUri($request, $route);
                if (empty($arguments) === false) {
                    return $arguments;
                }

                throw new \InvalidArgumentException('отсутствуют обязательные аргументы: ' . $param->name);
            }

            if ($paramExists === true) {
                $arguments[] = $request->getQueryParams()[$param->name];
            }

            if (empty($param->defaultValue) === false && $paramExists === false) {
                $arguments[] = $param->defaultValue;
            }
        }

        return $arguments;
    }

    private function mapArgsFromUri(ServerRequestInterface $request, Route $route): array
    {
        $pattern = preg_replace('#\{[^}]+\}#', '(\d+)', $route->path);
        $pattern = "#^" . $pattern . "$#";

        $arguments = [];
        if (preg_match($pattern, $request->getUri()->getScheme(), $matches)) {
            array_shift($matches);
            foreach ($matches as $match) {
                $arguments[] = $match;
            }
        }

        return $arguments;
    }

    private function prepareParams(string $route): array
    {
        if (preg_match_all('/{\s*(.*?)\s*}/', $route, $matches) === false) {
            return [];
        }

        $bindings = [];

        foreach ($matches[1] as $param) {
            $bindings[] = new RouteParameter($param);
        }

        return $bindings;
    }
}

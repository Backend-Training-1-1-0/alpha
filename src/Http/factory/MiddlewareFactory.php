<?php

namespace Alpha\Http\factory;

use Alpha\Components\DIContainer\DIContainer;
use Alpha\Contracts\HttpMiddlewareInterface;
use InvalidArgumentException;

class MiddlewareFactory
{
    public function __construct(
        private readonly DIContainer $container,
    ) { }

    public function createMiddlewareChain(array $middlewares): callable
    {
        $lastMiddleware = function ($request, $response) {
            // Это базовый обработчик. Вы можете модифицировать это поведение, если хотите что-то выполнить в конце цепочки.
            return $response;
        };

        while ($middleware = array_pop($middlewares)) {
            $next = $lastMiddleware;
            $lastMiddleware = function ($request, $response) use ($middleware, $next) {
                if (is_callable($middleware)) {
                    return $middleware($request, $response, $next);
                }
                if (is_string($middleware) === false) {
                    throw new InvalidArgumentException('Значение middlewares должно быть строкой');
                }
                /** @var HttpMiddlewareInterface $middlewareInstance */
                $middlewareInstance = $this->container->make($middleware);
                if ($middlewareInstance instanceof HttpMiddlewareInterface === false) {
                    throw new InvalidArgumentException("Неверный тип объекта $middleware");
                }
                return $middlewareInstance->execute($request, $response, $next);
            };
        }

        return $lastMiddleware;
    }
}
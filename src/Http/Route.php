<?php

namespace Http;

class Route
{
    public function __construct(
        public string $path,
        public string $method,
        public mixed $handler,
        public array $params,
        public ?string $action,
        public mixed $middleware,
        public array $groupStack
    ) {}
}

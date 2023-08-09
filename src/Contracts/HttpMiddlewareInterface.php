<?php

namespace Alpha\Contracts;

interface HttpMiddlewareInterface
{
    public function execute(HttpRequestInterface $request): void;
}

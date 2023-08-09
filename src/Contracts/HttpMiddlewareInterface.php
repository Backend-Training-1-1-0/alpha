<?php

namespace Contracts;

interface HttpMiddlewareInterface
{
    public function execute(HttpRequestInterface $request): void;
}

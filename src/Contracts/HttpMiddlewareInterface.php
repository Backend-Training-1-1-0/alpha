<?php

namespace Alpha\Contracts;

use Psr\Http\Message\RequestInterface;

interface HttpMiddlewareInterface
{
    public function execute(RequestInterface $request): void;
}

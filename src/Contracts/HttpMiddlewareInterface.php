<?php

namespace Alpha\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface HttpMiddlewareInterface
{
    public function execute(ServerRequestInterface $request): void;
}

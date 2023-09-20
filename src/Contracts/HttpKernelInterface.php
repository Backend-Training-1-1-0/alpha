<?php

namespace Alpha\Contracts;

use Psr\Http\Message\ResponseInterface;

interface HttpKernelInterface
{
    function handle(): ResponseInterface;
}

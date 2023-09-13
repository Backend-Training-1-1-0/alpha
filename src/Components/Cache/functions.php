<?php

use Psr\Cache\CacheItemPoolInterface;

function cache(): CacheItemPoolInterface
{
    return container()->make(CacheItemPoolInterface::class);
}

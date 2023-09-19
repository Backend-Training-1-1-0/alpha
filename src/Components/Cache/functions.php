<?php

use Psr\Cache\CacheItemPoolInterface;

function cache(): CacheItemPoolInterface
{
    return container()->get(CacheItemPoolInterface::class);
}

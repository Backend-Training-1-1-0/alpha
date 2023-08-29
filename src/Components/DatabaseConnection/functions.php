<?php

use Alpha\Components\DIContainer\DIContainer;
use Alpha\Contracts\DatabaseConnectionInterface;

function db(): DatabaseConnectionInterface
{
    return container()->make(DatabaseConnectionInterface::class);
}

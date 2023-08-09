<?php

namespace Alpha\Contracts;

interface HttpKernelInterface
{
    function handle(): HttpResponseInterface;
}

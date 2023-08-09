<?php

namespace Contracts;

interface HttpKernelInterface
{
    function handle(): HttpResponseInterface;
}

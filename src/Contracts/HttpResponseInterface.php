<?php

namespace Contracts;

interface HttpResponseInterface
{
    function __construct();

    function send(): never;

    function setHeader(string $name, string $header): void;

    function setBody(string $body): void;
}

<?php

namespace Alpha\Contracts;

interface HttpResponseInterface
{
    function send(): never;
    function setHeader(string $name, string $header): void;
    function setBody(string $body): void;
}

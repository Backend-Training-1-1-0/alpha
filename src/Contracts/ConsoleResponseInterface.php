<?php

namespace Alpha\Contracts;

interface ConsoleResponseInterface
{
    function send(): never;

    function setHeader(string $header, string $value): void;

    function setBody(string $body): void;
}

<?php

namespace Contracts;

interface MessageInterface
{
    function __construct(mixed $message);

    function getMessage(): mixed;

}

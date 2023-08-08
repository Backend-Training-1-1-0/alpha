<?php

namespace Components\EventDispatcher;

interface MessageInterface
{
    function __construct(mixed $message);

    function getMessage(): mixed;

}

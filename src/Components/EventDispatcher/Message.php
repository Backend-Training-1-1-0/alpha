<?php

namespace Components\EventDispatcher;

use Contracts\MessageInterface;

class Message implements MessageInterface
{
    public function __construct(private mixed $message) { }

    public function getMessage(): mixed
    {
        return $this->message;
    }
}

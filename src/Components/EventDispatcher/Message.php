<?php

namespace Components\EventDispatcher;

class Message implements MessageInterface
{
    public function __construct(private mixed $message) { }

    public function getMessage(): mixed
    {
        return $this->message;
    }
}

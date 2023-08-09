<?php

namespace Alpha\Contracts;

use Alpha\Components\EventDispatcher\Message;

interface ObserverInterface
{
    function observe($event, Message $message): void;
}

<?php

namespace Components\EventDispatcher;

use Modules\NumberGenerator\Event;

interface ObserverInterface
{
    function observe(Event $event, Message $message): void;
}

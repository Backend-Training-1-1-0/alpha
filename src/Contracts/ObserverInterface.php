<?php

namespace Contracts;

use Components\EventDispatcher\Message;
use Modules\NumberGenerator\Event;

interface ObserverInterface
{
    function observe(Event $event, Message $message): void;
}

<?php

namespace Components\EventDispatcher;

use Modules\NumberGenerator\Event;

interface EventDispatcherInterface
{
    function __construct();

    function attach(Event $event, ObserverInterface $observer): void;
    function detach(Event $event): void;
    function notify(Event $event, Message $message): void;
    function configure(array $config): void;
}

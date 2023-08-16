<?php

namespace Alpha\Contracts;

use Alpha\Contracts\ObserverInterface;
use Alpha\Components\EventDispatcher\Message;

interface EventDispatcherInterface
{
    function attach($event, ObserverInterface $observer): void;
    function detach($event): void;
    function notify($event, Message $message): void;
    function configure(array $config): void;
}

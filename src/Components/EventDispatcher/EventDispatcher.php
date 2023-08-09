<?php

namespace Components\EventDispatcher;

use Contracts\ObserverInterface;
use Contracts\EventDispatcherInterface;
use Modules\NumberGenerator\Event;

class EventDispatcher implements EventDispatcherInterface
{
    private array $observers = [];

    public function __construct() { }

    public function attach(Event $event, ObserverInterface $observer): void
    {
        $this->observers[$event->value][] = $observer;
    }

    public function detach(Event $event): void
    {
        if (isset($this->observers[$event->value]) === false) {
            return;
        }

        unset($this->observers[$event->value]);
    }

    public function notify(Event $event, Message $message): void
    {
        if (isset($this->observers[$event->value]) === false) {
            return;
        }

        foreach ($this->observers[$event->value] as $observer) {
            $observer->observe($event, $message);
        }
    }

    public function configure(array $config): void
    {
        if (empty($this->observers) === false) {
            throw new \RuntimeException('Объект сконфигурирован ранее');
        }

        foreach ($config as $value) {
            $this->attach(...$value);
        }
    }
}

<?php

namespace Alpha\Components\EventDispatcher;

use Alpha\Contracts\ObserverInterface;
use Alpha\Contracts\EventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    private array $observers = [];

    public function __construct() { }

    public function attach($event, ObserverInterface $observer): void
    {
        $this->observers[$event->value][] = $observer;
    }

    public function detach($event): void
    {
        if (isset($this->observers[$event->value]) === false) {
            return;
        }

        unset($this->observers[$event->value]);
    }

    public function notify($event, Message $message): void
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

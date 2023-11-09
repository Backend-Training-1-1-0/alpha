<?php

namespace Alpha\tests\Components;

use Alpha\Components\EventDispatcher\EventDispatcher;
use Alpha\Components\EventDispatcher\Message;
use Alpha\Contracts\ObserverInterface;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    /**
     * @covers EventDispatcher::attach
     * @covers EventDispatcher::notify
     */
    public function testAttachAndNotify()
    {
        $dispatcher = new EventDispatcher();
        $observer = $this->createMock(ObserverInterface::class);
        $event = (object) ['value' => 'event'];
        $message = $this->createMock(Message::class);

        $observer->expects($this->once())
            ->method('observe')
            ->with($event, $message);

        $dispatcher->attach($event, $observer);
        $dispatcher->notify($event, $message);
    }

    /**
     * @covers EventDispatcher::detach
     */
    public function testDetach()
    {
        $dispatcher = new EventDispatcher();
        $observer = $this->createMock(ObserverInterface::class);
        $event = (object) ['value' => 'event'];

        $dispatcher->attach($event, $observer);
        $dispatcher->detach($event);

        $observer->expects($this->never())
            ->method('observe');

        $dispatcher->notify($event, $this->createMock(Message::class));
    }

    /**
     * @covers EventDispatcher::configure
     */
    public function testConfigure()
    {
        $dispatcher = new EventDispatcher();
        $observer = $this->createMock(ObserverInterface::class);
        $event = (object) ['value' => 'event'];
        $config = [[$event, $observer]];

        $dispatcher->configure($config);

        $observer->expects($this->once())
            ->method('observe');

        $dispatcher->notify($event, $this->createMock(Message::class));
    }
}
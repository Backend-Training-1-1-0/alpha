<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Components\EventDispatcher\Message;
use Alpha\Contracts\ObserverInterface;

class SqlDebugger implements ObserverInterface
{
    private array $sqlLog;

    public function observe($event, Message $message): void
    {
        $this->sqlLog[] = $message->getMessage();
    }

    public function getSqlLog(): array
    {
        //TODO: реализация метода возврата лога
    }
}
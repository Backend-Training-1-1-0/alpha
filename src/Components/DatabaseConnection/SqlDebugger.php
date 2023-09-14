<?php

namespace Alpha\Components\DatabaseConnection;

use Alpha\Components\DIContainer\DIContainer;
use Alpha\Components\EventDispatcher\Message;
use Alpha\Contracts\ObserverInterface;
use Psr\Log\LoggerInterface;

class SqlDebugger implements ObserverInterface
{
    private array $sqlLog;
    private DIContainer $DIContainer;

    public function __construct()
    {
        $this->DIContainer = DIContainer::getInstance();
    }

    public function observe($event, Message $message): void
    {
        $this->sqlLog[] = [
            'query' => $message->getMessage(),
            'date' => date('Y-m-d H:i:s')
        ];
    }

    public function getSqlLog(): array
    {
        return $this->sqlLog;
    }

    public function writeToFile(string $message): void
    {
        $this->DIContainer->make(LoggerInterface::class)->debug($message, $this->getSqlLog());
    }
}
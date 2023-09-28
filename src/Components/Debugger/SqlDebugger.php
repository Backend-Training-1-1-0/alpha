<?php

namespace Alpha\Components\Debugger;

use Alpha\Components\EventDispatcher\Message;
use Alpha\Contracts\ObserverInterface;
use Psr\Log\LoggerInterface;

class SqlDebugger implements ObserverInterface
{
    private array $sqlLog;

    public function __construct(private readonly LoggerInterface $logger)
    {
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
        $this->logger->debug($message, $this->getSqlLog());
    }
}
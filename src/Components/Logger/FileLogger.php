<?php

namespace Alpha\Components\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class FileLogger implements LoggerInterface
{
    public string $projectIndex = 'ALPHA';
    public string $category = 'application';

    public function __construct(public string $logFile)
    {
    }

    public function emergency($message, array $context = array()): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = array()): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = array()): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = array()): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = array()): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = array()): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = array()): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = array()): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = array()): void
    {
        if (!$this->isValidLogLevel($level)) {
            throw new \InvalidArgumentException("Несуществующий уровень логгирования: $level");
        }

        $logMessage = $this->formatMessage($level, $message, $context);
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    private function isValidLogLevel($level): bool
    {
        return in_array($level, $this->getLevel());
    }

    public function getLevel(): array
    {
        return [
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
            LogLevel::ERROR,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
            LogLevel::EMERGENCY
        ];
    }

    private function formatMessage($level, $message, $context): string
    {
        $formatLog = [
            'index' => $this->projectIndex,
            'category' => $this->category,
            'context' => $context ?? '',
            'level' => array_keys($this->getLevel(), $level),
            'level_name' => $level,
            'action' => $context['action'] ?? '',
            'action_type' => $context['action_type'] ?? '',
            'datetime' => date('Y-m-d H:i:s'),
            'timestamp' => (new \DateTimeImmutable)->format('Y-m-d H:i:s.u'),
            'userId' => null,
            'ip' => $_SERVER['HTTP_X_REAL_IP'] ?? null,
            'real_ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
            'x_debug_tag' => $this->getXDebugTag(),
            'message' => $message,
            'exception' => $context['exception'] ?? '',
            'extras' => $context['exception'] ?? '',
        ];


        return json_encode($formatLog);
    }

    private function getXDebugTag(): string
    {
        return md5('x-debug-tag-' . $this->projectIndex . '-' . uniqid() . time());
    }
}
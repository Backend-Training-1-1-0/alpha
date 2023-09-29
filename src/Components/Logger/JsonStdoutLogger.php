<?php

namespace Alpha\Components\Logger;

class JsonStdoutLogger extends FileLogger
{
    public function log($level, $message, array $context = []): void
    {
        if (!$this->isValidLogLevel($level)) {
            throw new \InvalidArgumentException("Несуществующий уровень логгирования: $level");
        }

        $logMessage = $this->formatMessage($level, $message, $context);

        $fileHandle = fopen('php://stdout', 'w');

        if ($fileHandle) {
            fwrite($fileHandle, $logMessage);
            fclose($fileHandle);
        } else {
            error_log("Не удалось открыть php://stdout для записи");
        }
    }
}
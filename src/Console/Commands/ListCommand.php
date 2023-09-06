<?php

namespace Alpha\Console\Commands;

use Alpha\Console\CommandDefinition;
use Alpha\Contracts\{
    ConsoleCommandInterface,
    ConsoleInputInterface,
    ConsoleKernelInterface,
    ConsoleOutputInterface,
};

class ListCommand implements ConsoleCommandInterface
{
    private static string $signature = 'info';
    private static string $description = 'Вывод информации о доступных командах';
    private static bool $hidden = true;

    public function __construct(
        private readonly ConsoleInputInterface  $input,
        private readonly ConsoleOutputInterface $output,
        private readonly ConsoleKernelInterface $kernel,
    )
    {
        $this->input->bindDefinition($this);
    }

    static function getSignature(): string
    {
        return self::$signature;
    }

    static function getDescription(): string
    {
        return self::$description;
    }

    static function getHidden(): bool
    {
        return self::$hidden;
    }

    public function execute(): void
    {
        $commands = $this->kernel->getCommandMap();

        $this->output->warning("ЭФКО Фреймворк 0.0.1" . PHP_EOL);
        $this->output->stdout(PHP_EOL);
        $this->output->warning("Фреймворк создан разработчиками компании ЭФКО Цифровые решения." . PHP_EOL);
        $this->output->warning("Является платформой для изучения базового поведения приложения созданного на PHP." . PHP_EOL);
        $this->output->warning("Фреймворк не является production-ready реализацией и не предназначен для коммерческого использования" . PHP_EOL);
        $this->output->stdout(PHP_EOL);

        $this->output->success("Доступные опции:" . PHP_EOL);

        $definition = new CommandDefinition(self::getSignature());

        foreach ($definition->getCommonOptions() as $property => $propertyData) {
            $this->output->success('    ' . $property);
            $this->output->stdout(" : " . $propertyData['description'] . PHP_EOL);
        }

        $this->output->stdout(PHP_EOL);

        $this->output->success("Вызов:" . PHP_EOL);
        $this->output->stdout('    ' . 'команда [аргументы] [опции]');

        $this->output->stdout(PHP_EOL . PHP_EOL);

        $this->output->success("Доступные команды:" . PHP_EOL);

        foreach ($commands as $key => $command) {
            if ((bool)$command['isHidden'] === true) {
                continue;
            }
            $this->output->success('    ' . $key,);
            $this->output->stdout(" - {$command['description']}" . PHP_EOL);
        }
    }
}

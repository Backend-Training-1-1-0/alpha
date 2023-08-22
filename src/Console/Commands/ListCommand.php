<?php

namespace Alpha\Console\Commands;

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
    private array $commonProperty = [
        '--help' => 'Вывод информации о команде',
        '--interactive' => 'Вызов команды в режиме интерактивного ввода'
    ];

    public function __construct(
        private readonly ConsoleInputInterface $input,
        private readonly ConsoleOutputInterface $output,
        private readonly ConsoleKernelInterface $kernel,
    ) {
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

        $this->output->stdout("ЭФКО Фреймворк 0.0.1" . PHP_EOL, 'warning');
        $this->output->stdout(PHP_EOL);
        $this->output->stdout("Фреймворк создан разработчиками компании ЭФКО Цифровые решения." . PHP_EOL, 'warning');
        $this->output->stdout("Является платформой для изучения базового поведения приложения созданного на PHP." . PHP_EOL , 'warning');
        $this->output->stdout("Фреймворк не является production-ready реализацией и не предназначен для коммерческого использования" . PHP_EOL , 'warning');
        $this->output->stdout(  PHP_EOL);

        $this->output->stdout("Доступные опции:" . PHP_EOL, 'success');
        foreach ($this->commonProperty as $property => $propertyDescription) {
            $this->output->stdout(  '    ' . $property, 'success');
            $this->output->stdout(   " : ". $propertyDescription. PHP_EOL);
        }

        $this->output->stdout(PHP_EOL);

        $this->output->stdout("Вызов:" . PHP_EOL, 'success');
        $this->output->stdout('    ' . 'команда [аргументы] [опции]');

        $this->output->stdout(PHP_EOL . PHP_EOL);

        $this->output->stdout("Доступные команды:" . PHP_EOL, 'success');

        foreach ($commands as $key => $command) {
            if ((bool) $command['isHidden'] === true) {
                continue;
            }
            $this->output->stdout('    ' . $key, 'success');
            $this->output->stdout(" - {$command['description']}" . PHP_EOL);
        }
    }
}

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
        
        $this->output->stdout("\033[34mЭФКО Фреймворк 0.0.1\033[0m" . PHP_EOL);
        $this->output->stdout(PHP_EOL);
        $this->output->stdout("\033[33mФреймворк создан разработчиками компании ЭФКО Цифровые решения.\033[0m" . PHP_EOL); //желтый
        $this->output->stdout("\033[33mЯвляется платформой для изучения базового поведения приложения созданного на PHP.\033[0m" . PHP_EOL); //желтый
        $this->output->stdout("\033[33mФреймворк не является production-ready реализацией и не предназначен для коммерческого использования\033[0m" . PHP_EOL); //желтый
        $this->output->stdout(  PHP_EOL);

        $this->output->stdout("\033[32mДоступные опции:\033[0m" . PHP_EOL);
        foreach ($this->commonProperty as $property => $propertyDescription) {
            $this->output->stdout(  '    ' . "\033[32m".$property ."\033[0m". " : ". $propertyDescription. PHP_EOL);
        }

        $this->output->stdout(PHP_EOL);

        $this->output->stdout("\033[32mВызов:\033[0m" . PHP_EOL);
        $this->output->stdout('    ' . 'команда [аргументы] [опции]');
        
        $this->output->stdout(PHP_EOL . PHP_EOL);

        if ($this->input->hasArgument('commandName') === false) {
            $this->output->stdout("\033[32mДоступные команды:\033[0m" . PHP_EOL);

            foreach ($commands as $key => $command) {
                if ((bool) $command['isHidden'] === true) {
                    continue;
                }
                $this->output->stdout('    ' . "\033[32m$key\033[0m - {$command['description']}" . PHP_EOL);
            }
        }
    }
}

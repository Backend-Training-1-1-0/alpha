<?php

namespace Console\Commands;

use Components\ConsoleInput\ConsoleInput;
use Contracts\{
    ConsoleCommandInterface,
    ConsoleKernelInterface,
};

class InfoCommand implements ConsoleCommandInterface
{
    const COMMAND_NAME = 'info {?commandName}';
    const COMMAND_ARGUMENTS = ['commandName'=> ['description' => 'имя команды', 'isRequired'=> 'не обязательный параметр']];
    const COMMAND_DESCRIPTION = 'Вывод информации о доступных командах';

    public function __construct(
        private ConsoleKernelInterface $kernel,
        public ConsoleInput $input
    ) { }

    static function getName(): string
    {
        return self::COMMAND_NAME;
    }

    static function getDescription(): string
    {
        return self::COMMAND_DESCRIPTION;
    }

    function getCommandInfo(array $args): void
    {
        $this->getInfo();
    }

    public function execute(): void
    {
        if ($this->input->hasArgument('commandName') === true) {
             $this->kernel->dispatch(['./bin', $this->input->getArgument('commandName'), '-h']);
            return;
        }
        $commands = $this->kernel->getCommandMap();

        $this->getInfo();

        echo "\033[32mВызов:\033[0m" . PHP_EOL;

        foreach ($commands as $key => $command) {
            if ((bool) $command['isHidden'] === true){
                echo " " . "$key " . "[" . 'commandName' . "]" .  PHP_EOL;
                echo PHP_EOL;
                echo "\033[34mНазначение:\033[0m" . PHP_EOL;
                echo " " . $command['description'] . PHP_EOL;
                echo PHP_EOL;
            }
        }

        echo "\033[34mАргументы:\033[0m" . PHP_EOL;
        $arguments = $this->getArguments();
        foreach ($arguments as $key => $argument) {

            echo "\033[32m$key:\033[0m". ' ' . $argument['description'] . ', ' . $argument['isRequired'] . PHP_EOL;

        }
        echo PHP_EOL;

        echo "\033[32mДоступные команды:\033[0m" . PHP_EOL;

        foreach ($commands as $key => $command) {
            if ((bool) $command['isHidden'] === true) {
                continue;
            }
            echo "\033[32m$key\033[0m - {$command['description']}" . PHP_EOL;
        }

    }

    public static function isHidden(): bool
    {
        return true;
    }

    private function getInfo(): void
    {
        echo "\033[34mЭФКО Фреймворк 0.0.1\033[0m" . PHP_EOL;
        echo PHP_EOL;
        echo "\033[33mФреймворк создан разработчиками компании ЭФКО Цифровые решения.\033[0m" . PHP_EOL; //желтый
        echo "\033[33mЯвляется платформой для изучения базового поведения приложения созданного на PHP.\033[0m" . PHP_EOL; //желтый
        echo "\033[33mФреймворк не является production-ready реализацией и не предназначен для коммерческого использования\033[0m" . PHP_EOL; //желтый
        echo  PHP_EOL;
    }
    function getArguments(): array
    {
        return self::COMMAND_ARGUMENTS;
    }
}

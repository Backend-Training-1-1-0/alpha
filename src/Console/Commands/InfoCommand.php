<?php

namespace Alpha\Console\Commands;

use Alpha\Console\CommandDefinition;
use Alpha\Contracts\{
    ConsoleCommandInterface,
    ConsoleInputInterface,
    ConsoleKernelInterface,
    ConsoleOutputInterface,
};

class InfoCommand implements ConsoleCommandInterface
{
    private static string $signature = 'info {?commandName:имя команды}';
    private static string $description = 'Вывод информации о доступных командах';
    private static bool $hidden = true;

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

        $definition = new CommandDefinition(self::getSignature());
        $description = self::$description;

        if ($this->input->hasArgument('commandName') === true) {
            if (isset($commands[$this->input->getArgument('commandName')]) === false) {
                throw new \InvalidArgumentException('Передано имя несуществующей команды');
            }

            $namespaceCommand = $commands[$this->input->getArgument('commandName')]['namespace'];

            $definition = new CommandDefinition(call_user_func([$namespaceCommand, 'getSignature']));
            $description = $namespaceCommand::$description;
        }

        $commandName = $definition->commandName;

        echo "\033[34mЭФКО Фреймворк 0.0.1\033[0m" . PHP_EOL;
        echo PHP_EOL;
        echo "\033[33mФреймворк создан разработчиками компании ЭФКО Цифровые решения.\033[0m" . PHP_EOL; //желтый
        echo "\033[33mЯвляется платформой для изучения базового поведения приложения созданного на PHP.\033[0m" . PHP_EOL; //желтый
        echo "\033[33mФреймворк не является production-ready реализацией и не предназначен для коммерческого использования\033[0m" . PHP_EOL; //желтый
        echo  PHP_EOL;

        $arguments = $definition->arguments;
        $options = $definition->options;

        echo "\033[32mВызов:\033[0m" . PHP_EOL;

        echo  "$commandName";

        if (count($arguments) > 0) {
            foreach ($arguments as $key => $value) {
                echo  " [$key]";
            }
        }

        if (count($options) > 0) {
            echo  ' [опции]';
        }

        echo PHP_EOL . PHP_EOL;

        echo "\033[34mНазначение:\033[0m" . PHP_EOL;
        echo $description . PHP_EOL;

        echo PHP_EOL;

        if (count($arguments) > 0) {
            echo "\033[34mАргументы:\033[0m" . PHP_EOL;

            foreach ($arguments as $key => $argument) {
                echo "\033[32m$key:\033[0m". ' ' . $argument['description'] . ', ' . ($argument['required'] ? 'обязательный параметр' : 'не обязательный параметр') . PHP_EOL;
            }
        }

        echo PHP_EOL;

        if (count($options) > 0) {
            echo "\033[34mОпции:\033[0m" . PHP_EOL;

            foreach ($options as $key => $option) {
                echo "\033[32m$key:\033[0m". ' ' . $option['description'] . PHP_EOL;
            }
        }

        echo PHP_EOL;

        if ($this->input->hasArgument('commandName') === false) {
            echo "\033[32mДоступные команды:\033[0m" . PHP_EOL;

            foreach ($commands as $key => $command) {
                if ((bool) $command['isHidden'] === true) {
                    continue;
                }
                echo "\033[32m$key\033[0m - {$command['description']}" . PHP_EOL;
            }
        }
    }
}

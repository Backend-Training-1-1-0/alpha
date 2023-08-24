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
    private array $commonProperty = ['--interactive' => 'опция "Интерактивный режим"'];

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

        $commandName = $definition->getCommandName();
        $this->output->stdout("\033[34mЭФКО Фреймворк 0.0.1\033[0m" . PHP_EOL);
        $this->output->stdout("\033[34mЭФКО Фреймворк 0.0.1\033[0m" . PHP_EOL);
        $this->output->stdout(PHP_EOL);
        $this->output->stdout("\033[33mФреймворк создан разработчиками компании ЭФКО Цифровые решения.\033[0m" . PHP_EOL); //желтый
        $this->output->stdout("\033[33mЯвляется платформой для изучения базового поведения приложения созданного на PHP.\033[0m" . PHP_EOL); //желтый
        $this->output->stdout("\033[33mФреймворк не является production-ready реализацией и не предназначен для коммерческого использования\033[0m" . PHP_EOL); //желтый
        $this->output->stdout(  PHP_EOL);

        $arguments = $definition->getArguments();
        $options = $definition->getOptions();

        $this->output->stdout("\033[34mОбщие опции:\033[0m" . PHP_EOL);
        foreach ($this->commonProperty as $property => $description) {
            $this->output->stdout( "\033[32m".$property ."\033[0m". " : ". $description. PHP_EOL);
        }

        $this->output->stdout(PHP_EOL);

        $this->output->stdout("\033[32mВызов:\033[0m" . PHP_EOL);

        $this->output->stdout("$commandName");

        if (count($arguments) > 0) {
            foreach ($arguments as $key => $value) {
                $this->output->stdout(" [$key]");
            }
        }

        if (count($options) > 0) {
            $this->output->stdout(' [опции]');
        }


        $this->output->stdout(PHP_EOL . PHP_EOL);

        $this->output->stdout("\033[34mНазначение:\033[0m" . PHP_EOL);
        $this->output->stdout($description . PHP_EOL);

        $this->output->stdout(PHP_EOL);

        if (count($arguments) > 0) {
            $this->output->stdout("\033[34mАргументы:\033[0m" . PHP_EOL);

            foreach ($arguments as $key => $argument) {
                $this->output->stdout("\033[32m$key:\033[0m". ' ' . $argument['description'] . ', ' . ($argument['required'] ? 'обязательный параметр' : 'не обязательный параметр') . PHP_EOL);
            }
        }

        $this->output->stdout(PHP_EOL);

        if (count($options) > 0) {
            $this->output->stdout("\033[34mОпции:\033[0m" . PHP_EOL);

            foreach ($options as $key => $option) {
                $this->output->stdout("\033[32m$key:\033[0m". ' ' . $option['description'] . PHP_EOL);
            }
        }

        $this->output->stdout(PHP_EOL);

        if ($this->input->hasArgument('commandName') === false) {
            $this->output->stdout("\033[32mДоступные команды:\033[0m" . PHP_EOL);

            foreach ($commands as $key => $command) {
                if ((bool) $command['isHidden'] === true) {
                    continue;
                }
                $this->output->stdout("\033[32m$key\033[0m - {$command['description']}" . PHP_EOL);
            }
        }
    }
}

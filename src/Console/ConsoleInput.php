<?php

namespace Alpha\Console;

use Alpha\Contracts\ConsoleCommandInterface;
use Alpha\Contracts\ConsoleInputInterface;

class ConsoleInput implements ConsoleInputInterface
{
    public array $arguments = [];
    public array $options = [];
    public CommandDefinition $definition;
    private array $tokens = [];

    public function __construct()
    {
        $this->tokens = array_slice($_SERVER['argv'], 2);
    }

    public function bindDefinition(ConsoleCommandInterface $command):void
    {
        $this->arguments = [];
        $this->options = [];
        $this->definition = new CommandDefinition($command::getSignature());

        $this->parse();
        $this->validate();
        $this->setDefaults();
    }

    private function parse(): void
    {
        $listKeys = array_keys($this->definition->arguments);

        foreach ($this->tokens as $key => $value) {
            if (str_contains($value, '--') === false) {
                $paramName = $listKeys[$key];

                $this->arguments[$paramName] = $value;
            }

            if (str_contains($value, '--') === true) {
                $this->options[] = $value;
            }
        }

        $this->validateOptions();

        if ($this->hasOption('--interactive') === true) {
            foreach ($this->definition->arguments as $key => $value) {
                $default = empty($value['default']) === false ? "[{$value['default']}]" : '';

                $this->arguments[$key] = $this->getInput($key, "Введите $key ({$value["description"]}) $default:");
            }

            foreach ($this->definition->options as $key => $value) {
                $this->askForApproval($key, $value);
            }
        }
    }

    private function validate(): void
    {
        foreach ($this->definition->arguments as $paramName => $paramProperties) {
            $isExists = in_array($paramName, array_keys($this->arguments));

            if ($paramProperties['required'] === true && $isExists === false) {
                throw new \InvalidArgumentException('отсутствуют обязательные аргументы: ' . $paramName);
            }
        }
    }

    private function setDefaults(): void
    {
        foreach ($this->definition->arguments as $paramName => $paramProperties) {
            if (empty($this->arguments[$paramName]) === true && $paramProperties['default'] !== null) {
                $this->arguments[$paramName] = $paramProperties['default'];
            }
        }
    }

    public function hasOption(string $option): bool
    {
        return in_array($option, $this->options);
    }

    public function getArgument(string $argument): mixed
    {
        if (empty($this->arguments[$argument]) === true) {
            return false;
        }

        return is_numeric($this->arguments[$argument])
            ? (int)$this->arguments[$argument]
            : $this->arguments[$argument];
    }

    public function hasArgument(string $argument): bool
    {
        return empty($this->arguments[$argument]) === false;
    }

    public function getInput(string $argumentName, string $prompt): mixed
    {
        echo "$prompt" . PHP_EOL;

        return trim(fgets(STDIN));
    }

    public function askForApproval(string $key, array $value): void
    {
        echo "Применить опцию $key? ({$value["description"]}) [да] да/нет" . PHP_EOL;
        $approval = trim(fgets(STDIN));

        if ($approval === '' || $approval === 'да') {
            $this->options[] = $key;
        }
    }

    private function validateOptions(): void
    {
        $optionsNames = array_keys($this->definition->options);
        foreach ($this->options as $option) {
            if (in_array($option, $optionsNames) === false && $this->hasOption('--interactive') === false) {
                throw new \InvalidArgumentException('Введена несуществующая опция ' . $option);
            }
        }
    }
}

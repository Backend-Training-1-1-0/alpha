<?php

namespace Alpha\Console;

use Alpha\Contracts\ConsoleCommandInterface;
use Alpha\Contracts\ConsoleInputInterface;

class ConsoleInput implements ConsoleInputInterface
{
    public array $arguments = [];
    public array $options = [];
    public CommandDefinition $definition;
    private array $params = [];

    public function __construct() { }

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

        foreach ($this->params as $key => $value) {
            if (str_contains($value, '--') === false) {
                $paramName = $listKeys[$key];

                $this->arguments[$paramName] = $value;
            }

            if (str_contains($value, '--') === true) {
                $this->options[] = $value;
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
            if ($paramProperties['default'] !== null) {
                $this->arguments[$paramName] = $paramProperties['default'];
            }
        }
    }

    public function getOption(string $option): mixed
    {
        if (empty($this->options[$option]) === false) {
            return $this->options[$option];
        }

        return false;
    }

    public function hasOption(string $option): bool
    {
        return empty($this->options[$option]) === false;
    }

    public function getArgument(string $argument): mixed
    {
        if (empty($this->arguments[$argument] === false)) {
            return $this->arguments[$argument];
        }

        return false;
    }

    public function hasArgument(string $argument): bool
    {
        return empty($this->arguments[$argument]) === false;
    }
}
<?php

namespace Alpha\Console;

use Alpha\Contracts\ConsoleCommandInterface;
use Alpha\Contracts\ConsoleInputInterface;

class ConsoleInput implements ConsoleInputInterface
{
    public array $arguments = [];
    public array $options = [];
    public CommandDefinition $definition;

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
        $params = array_slice($_SERVER['argv'], 2);

        foreach ($params as $key => $value) {
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

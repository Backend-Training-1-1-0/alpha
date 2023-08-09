<?php

namespace Components\ConsoleInput;

use Contracts\ConsoleInputInterface;

class ConsoleInput implements ConsoleInputInterface
{
    public array $arguments = [];

    public array $options = [];

    public function __construct() { }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
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

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
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

    public function removeArgumentStartingWithDoubleDash(): void
    {
        foreach ($this->arguments as $key => $argument) {
            if (str_starts_with($key, '--')) {
                unset($this->arguments[$key]);
            }
        }
    }
}

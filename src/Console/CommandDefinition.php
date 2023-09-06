<?php

namespace Alpha\Console;

class CommandDefinition
{
    private string $commandName = '';
    private array $arguments = [];
    private array $options = [];

    public function __construct(
        private readonly string $signature,
        public readonly string $description = '',
    )
    {
        $this->initDefinitions();
    }

    public function getCommandName(): string
    {
        return $this->commandName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getCommonOptions() :array
    {
        return $this->commonOptions;
    }

    public function setOption(array $option): void
    {
        $this->options += $option;
    }

    private function initDefinitions(): void
    {
        preg_match('/^([^{\s]+)/u', $this->signature, $matches);
        $this->commandName = $matches[1];

        preg_match_all('/{([^}]+)}/u', $this->signature, $matches);

        foreach ($matches[1] as $prepareString) {
            $parts = explode(':', $prepareString);
            $name = ltrim(trim($parts[0] ?? ''), '?');
            $description = isset($parts[1]) ? trim($parts[1]) : '';

            if (str_contains($name, '--')) {
                $this->initOption($name, $description);

                continue;
            }

            $this->initArgument($name, $description);
        }
    }

    private function initOption(string $name, string $description): void
    {
        $this->options[$name] = [
            'description' => $description,
        ];
    }

    private function initArgument(string $name, string $description): void
    {
        if (substr_count($name, "=") === 1 && str_ends_with($name, "=") === false) {
            $arr = explode("=", $name);
            $this->arguments[$arr[0]] = [
                'description' => $description,
                'required' => false,
                'default' => $arr[1],
            ];
            return;
        }

        $this->arguments[$name] = [
            'description' => $description,
            'required' => true,
            'default' => null,
        ];
    }
}
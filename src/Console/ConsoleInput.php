<?php

namespace Alpha\Console;

use Alpha\Contracts\{
    ConsoleCommandInterface,
    ConsoleInputInterface,
};

class ConsoleInput implements ConsoleInputInterface
{
    public array $arguments = [];
    public array $options = [];
    private readonly CommandDefinition $definition;
    private array $tokens = [];
    private array $plugins = [];

    public function __construct()
    {
        $this->tokens = array_slice($_SERVER['argv'], 2);
    }

    public function bindDefinition(ConsoleCommandInterface $command):void
    {
        $this->arguments = [];
        $this->options = [];
        $this->definition = new CommandDefinition(
            $command::getSignature(),
            $command::getDescription()
        );

        $this->parse();
        $this->executePlugins();
        $this->validate();
        $this->validateOptions();
        $this->setDefaults();
    }

    public function addPlugins(array $plugins): void
    {
        $this->plugins = $plugins;
    }

    public function hasOption(string $option): bool
    {
        return in_array($option, $this->options);
    }

    public function getArgument(string $argument): mixed
    {
        if (empty($this->arguments[$argument]) === true) {
            throw new \InvalidArgumentException('Передан несуществующий аргумент ' . $argument);
        }

        return $this->arguments[$argument];
    }

    public function hasArgument(string $argument): bool
    {
        return empty($this->arguments[$argument]) === false;
    }

    public function getDefinition(): CommandDefinition
    {
        return $this->definition;
    }

    private function parse(): void
    {
        $listKeys = array_keys($this->definition->getArguments());

        foreach ($this->tokens as $key => $value) {
            if (str_contains($value, '--') === false) {
                $paramName = $listKeys[$key];

                $this->arguments[$paramName] = is_numeric($value) ? (int)$value : $value;
            }

            if (str_contains($value, '--') === true) {
                $this->options[] = $value;
            }
        }
    }

    private function executePlugins()
    {
        foreach ($this->plugins as $plugin) {
            /* @var ConsoleInputPluginInterface $pluginHandler*/
            $pluginHandler = container()->build($plugin);

            $pluginHandler->define($this);

            if ($pluginHandler->isSuitable($this)) {
                $pluginHandler->handle($this);
            }
        }
    }

    private function validate(): void
    {
        foreach ($this->definition->getArguments() as $paramName => $paramProperties) {
            $isExists = in_array($paramName, array_keys($this->arguments));

            if ($paramProperties['required'] === true && $isExists === false) {
                throw new \InvalidArgumentException('отсутствуют обязательные аргументы: ' . $paramName);
            }
        }
    }

    private function setDefaults(): void
    {
        foreach ($this->definition->getArguments() as $paramName => $paramProperties) {
            if (empty($this->arguments[$paramName]) === true && $paramProperties['default'] !== null) {
                $this->arguments[$paramName] = $paramProperties['default'];
            }
        }
    }

    private function validateOptions(): void
    {
        $options = $this->definition->getOptions();

        $optionsNames = array_merge(array_keys($options), array_column($options, 'shortcut'));

        foreach ($this->options as $option) {
            if (
                in_array($option, $optionsNames) === false
            ) {
                throw new \InvalidArgumentException('Введена несуществующая опция ' . $option);
            }
        }
    }
}

<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;
use Alpha\Contracts\ConsoleKernelInterface;

class CommandDetachOptionPlugin implements ConsoleInputPluginInterface
{
    public function __construct(private ConsoleKernelInterface $consoleKernel) {}

    private array $option = [
        '--detach' => [
            'description' => 'Перевод команды в фоновый режим',
            'isHidden' => true,
            'shortcut' => '--d',
        ]
    ];

    //TODO: вынести в абстрактный класс
    public function define(ConsoleInputInterface $input): void
    {
        $definition = $input->getDefinition();
        $definition->setOption($this->option);
    }

    //TODO: вынести в абстрактный класс
    public function isSuitable(ConsoleInputInterface $input): bool
    {
        $optionName = array_key_first($this->option);
        return $input->hasOption($optionName) === true
            || $input->hasOption($this->option[$optionName]['shortcut']) === true;
    }

    public function handle(ConsoleInputInterface $input): void
    {
        $argumentsString = implode(' ', array_values($input->arguments));

        $options = array_diff($input->options, ['--detach', '--d']);

        $optionsString = implode(' ', $options);

        $commandName = $input->getDefinition()->getCommandName();

        exec("php ./bin $commandName $argumentsString $optionsString > /dev/null 2>&1 &");

        $this->consoleKernel->terminate();
    }
}
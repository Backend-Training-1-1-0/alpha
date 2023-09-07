<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;
use Alpha\Contracts\ConsoleKernelInterface;

class CommandDetachOptionPlugin extends BaseCommandPlugin
{
    public function __construct(private ConsoleKernelInterface $consoleKernel) {}

    protected array $option = [
        '--detach' => [
            'description' => 'Перевод команды в фоновый режим',
            'isHidden' => true,
            'shortcut' => '--d',
        ]
    ];

    public function handle(ConsoleInputInterface $input): void
    {
        $argumentsString = implode(' ', array_values($input->arguments));

        $options = array_diff($input->options, ['--detach', '--d']);

        $optionsString = implode(' ', $options);

        $commandName = $input->getDefinition()->getCommandName();

        exec("./bin $commandName $argumentsString $optionsString");

        $this->consoleKernel->terminate();
    }
}
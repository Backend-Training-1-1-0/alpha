<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;
use Alpha\Contracts\ConsoleKernelInterface;

class CommandDetachOptionPlugin implements ConsoleInputPluginInterface
{
    public function __construct(private ConsoleKernelInterface $consoleKernel) {}

    public function isSuitable(ConsoleInputInterface $input): bool
    {
        return $input->hasOption('--detach') === true || $input->hasOption('--d') === true;
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
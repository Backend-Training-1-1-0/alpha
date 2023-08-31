<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;
use Alpha\Contracts\ConsoleKernelInterface;

class CommandDetachOptionPlugin implements ConsoleInputPluginInterface
{
    public function __construct(private ConsoleKernelInterface $consoleKernel) {}

    function isSuitable(ConsoleInputInterface $input): bool
    {
        return $input->hasOption('--detach') === true || $input->hasOption('--d') === true;
    }

    function handle(ConsoleInputInterface $input): void
    {
        $arguments = $input->getDefinition()->getArguments();

        $argumentsString = '';
        foreach ($arguments as $key => $value) {
            $argumentsString .= $input->getArgument($key) . ' ';
        }

        $commandName = $input->getDefinition()->getCommandName();

        exec("php ./bin $commandName $argumentsString > /dev/null 2>&1 &");

        $this->consoleKernel->terminate();
    }
}
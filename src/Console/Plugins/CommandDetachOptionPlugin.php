<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;
use Alpha\Contracts\ConsoleKernelInterface;

class CommandDetachOptionPlugin extends BaseCommandPlugin
{
    public function __construct(private ConsoleKernelInterface $consoleKernel) {}

    public function isSuitable(ConsoleInputInterface $input): bool
    {
        return $input->hasOption('--detach') === true || $input->hasOption('-d') === true;
    }
    protected array $option = [
        '--detach' => [
            'description' => 'Перевод команды в фоновый режим',
            'isHidden' => true,
            'shortcut' => '-d',
        ]
    ];

    public function handle(ConsoleInputInterface $input): void
    {
        $argumentsString = implode(' ', array_values($input->arguments));

        $options = array_diff($input->options, ['--detach', '-d']);

        $optionsString = implode(' ', $options);

        $commandName = $input->getDefinition()->getCommandName();


        $pid = pcntl_fork();

        if ($pid == -1) {
            die("Ошибка при создании дочернего процесса.");
        } elseif ($pid) {
            exec("./bin $commandName $argumentsString $optionsString");
            exit();
        } else {
            $this->consoleKernel->terminate();
        }
    }
}
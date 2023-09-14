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

        $command = "./bin $commandName $argumentsString $optionsString";
        $descriptorSpec = [
            ['pty'],  // stdin
            ['pty'],  // stdout
            ['pty'],  // stderr
        ];

        $process = proc_open($command, $descriptorSpec, $pipes);

        if (is_resource($process)) {
            // Закрываю все дескрипторы, чтобы отсоединить выполнение команды
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }

        $this->consoleKernel->terminate();
    }
}
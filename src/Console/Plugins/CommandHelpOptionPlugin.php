<?php

namespace Alpha\Console\Plugins;

use Alpha\Console\CommandDefinition;
use Alpha\Console\ConsoleKernel;
use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;
use Alpha\Contracts\ConsoleOutputInterface;

class CommandHelpOptionPlugin implements ConsoleInputPluginInterface
{
    private CommandDefinition $definition;

    public function __construct(
        private readonly ConsoleOutputInterface $output
    )
    {
    }

    function isSuitable(ConsoleInputInterface $input): bool
    {
        return $input->hasOption('--help') === true || $input->hasOption('--h') === true;
    }

    function handle(ConsoleInputInterface $input): void
    {
        $this->definition = $input->getDefinition();
        $this->printCommandInfo();

        container()->call(ConsoleKernel::class, 'terminate');
    }

    public function printCommandInfo()
    {
        $argsString = '{' . implode('}{', array_keys($this->definition->getArguments())) . '}';

        $optionsString =  $this->definition->getOptions() === [] ? '' : '[опции]';
        $this->output->info("Вызов:" . PHP_EOL);
        $this->output->stdout("    " .
            $this->definition->getCommandName() . " " .
            $argsString . ' ' .
            $optionsString.
            PHP_EOL);

        $this->output->info("Назначение:" . PHP_EOL);
        $this->output->stdout('    ' . $this->definition->description . PHP_EOL);

        $this->output->info("Аргументы:" . PHP_EOL, 'info');
        foreach ($this->definition->getArguments() as $name => $argData) {
            $isRequiredString = $argData['required'] === true ? 'обязательный параметр' : 'необязательный параметр';

            $descriptionOutput = [  $argData['description'], $isRequiredString];

            if($argData['default'] !== null) {
                $descriptionOutput[] = 'значение по умолчанию: ' . $argData['default'];
            }

            $this->output->success( '    ' . $name . ' ');
            $this->output->stdout(  implode(', ' , $descriptionOutput) . PHP_EOL);
        }

        $this->output->info("Опции:" . PHP_EOL);
        foreach ($this->definition->getOptions() as $name => $optionData) {
            if (isset($optionData['isHidden']) && $optionData['isHidden'] === true) {
                continue;
            }
            $this->output->success( '    ' . $name);
            $this->output->stdout( ' ' . $optionData['description'] . PHP_EOL);
        }
    }
}
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

    private array $option = [
        '--help' => [
            'description' => 'Вывод информации о команде',
            'isHidden' => true,
            'shortcut' => '--h',
        ],
    ];

    public function __construct(
        private readonly ConsoleOutputInterface $output
    )
    {
    }

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
        $this->definition = $input->getDefinition();
        $this->printCommandInfo();

        container()->call(ConsoleKernel::class, 'terminate');
    }

    private function printCommandInfo(): void
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
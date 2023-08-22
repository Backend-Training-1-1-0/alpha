<?php

namespace Alpha\Console\Components;

use Alpha\Console\CommandDefinition;
use Alpha\Contracts\ConsoleOutputInterface;

class CommandInfoService
{
    private CommandDefinition $definition;
    public function __construct(
        private ConsoleOutputInterface $output
    )
    {
    }

    public function setDefinition(CommandDefinition $definition)
    {
        $this->definition = $definition;
    }
    public function printCommandInfo()
    {
        $argsString = '{' . implode('}{', array_keys($this->definition->arguments)) . '}';

        $optionsString =  $this->definition->options === [] ? '' : '[опции]';
        $this->output->stdout("Вызов:" . PHP_EOL , 'info');
        $this->output->stdout("    " .
            $this->definition->commandName . " " .
            $argsString . ' ' .
            $optionsString.
            PHP_EOL);

        $this->output->stdout("Назначение:" . PHP_EOL, 'info');
        $this->output->stdout('    ' . $this->definition->description . PHP_EOL);

        $this->output->stdout("Аргументы:" . PHP_EOL, 'info');
        foreach ($this->definition->arguments as $name => $argData) {
            $isRequiredString = $argData['required'] === true ? 'обязательный параметр' : 'необязательный параметр';

            $descriptionOutput = [  $argData['description'], $isRequiredString];

            if($argData['default'] !== null) {
                $descriptionOutput[] = 'значение по умолчанию: ' . $argData['default'];
            }

            $this->output->stdout( '    ' . $name . ' ', 'success');
            $this->output->stdout(  implode(', ' , $descriptionOutput) . PHP_EOL);
        }

        $this->output->stdout("Опции:" . PHP_EOL, 'info');
        foreach ($this->definition->options as $name => $optionData) {
            $this->output->stdout( '    ' . $name, 'success');
            $this->output->stdout( ' ' . $optionData['description'] . PHP_EOL);
        }
    }
}
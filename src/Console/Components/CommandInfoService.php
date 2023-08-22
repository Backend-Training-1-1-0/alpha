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
        $this->output->stdout("\033[34mВызов:\033[0m" . PHP_EOL);
            $this->output->stdout("    " .
                $this->definition->commandName . " " .
                $argsString . ' ' . 
                $optionsString.
            PHP_EOL);

        $this->output->stdout("\033[34mНазначение:\033[0m" . PHP_EOL);
        $this->output->stdout('    ' . $this->definition->description . PHP_EOL);

        $this->output->stdout("\033[34mАргументы:\033[0m" . PHP_EOL);
        foreach ($this->definition->arguments as $name => $argData) {
            $isRequiredString = $argData['required'] === true ? 'обязательный параметр' : 'необязательный параметр';
            
            $descriptionOutput = [  $argData['description'], $isRequiredString];
            
            if($argData['default'] !== null) {
                $descriptionOutput[] = 'значение по умолчанию: ' . $argData['default'];
            }
            
            $this->output->stdout( '    ' . "\033[32m" .$name . "\033[0m" . ' ' .
                implode(', ' , $descriptionOutput) . PHP_EOL);
        }

        $this->output->stdout("\033[34mОпции:\033[0m" . PHP_EOL);
        foreach ($this->definition->options as $name => $optionData) {
            $this->output->stdout( '    ' . "\033[32m" .$name . "\033[0m" . 
                ' ' . $optionData['description'] . PHP_EOL);
        }
    }
}
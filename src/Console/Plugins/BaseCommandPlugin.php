<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;

abstract class BaseCommandPlugin implements ConsoleInputPluginInterface
{
    protected array $option;

    public function define(ConsoleInputInterface $input): void
    {
        $definition = $input->getDefinition();
        $definition->setOption($this->option);
    }

    public function isSuitable(ConsoleInputInterface $input): bool
    {
        $optionName = array_key_first($this->option);
        return $input->hasOption($optionName) === true
            || (isset($this->option[$optionName]['shortcut'])
                && $input->hasOption($this->option[$optionName]['shortcut']) === true);
    }
    
    abstract function handle(ConsoleInputInterface $input): void;
}
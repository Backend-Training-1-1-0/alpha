<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\{
    ConsoleInputInterface,
    ConsoleInputPluginInterface,
};

class CommandInteractiveOptionPlugin implements ConsoleInputPluginInterface
{
    private array $option = [
        '--interactive' => [
            'description' => 'Вызов команды в режиме интерактивного ввода',
            'isHidden' => true,
            'shortcut' => '--na',
        ],
    ];

    public function __construct()
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
        foreach ($input->getDefinition()->getArguments() as $key => $value) {
            $default = empty($value['default']) === false ? "[{$value['default']}]" : '';

            $input->arguments[$key] = $this->getInput($key, "Введите $key ({$value["description"]}) $default:");
        }

        foreach ($input->getDefinition()->getOptions() as $key => $value) {
            if (isset($value['isHidden']) === false) {
                $this->askForApproval($input, $key, $value);
            }
        }
    }

    private function getInput(string $argumentName, string $prompt): mixed
    {
        echo "$prompt" . PHP_EOL;

        $result = trim(fgets(STDIN));

        return is_numeric($result) ? (int)$result : $result;
    }

    private function askForApproval(ConsoleInputInterface $input, string $key, array $value): void
    {
        echo "Применить опцию $key? ({$value["description"]}) [да] да/нет" . PHP_EOL;
        $approval = trim(fgets(STDIN));

        if ($approval === '' || $approval === 'да') {
            $input->options[] = $key;
        }
    }
}
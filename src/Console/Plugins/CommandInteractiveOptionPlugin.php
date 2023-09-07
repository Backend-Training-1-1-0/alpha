<?php

namespace Alpha\Console\Plugins;

use Alpha\Contracts\{
    ConsoleInputInterface,
    ConsoleOutputInterface
};

class CommandInteractiveOptionPlugin extends BaseCommandPlugin
{
    protected array $option = [
        '--interactive' => [
            'description' => 'Вызов команды в режиме интерактивного ввода',
            'isHidden' => true,
            'shortcut' => '-na',
        ],
    ];

    public function __construct(
        private readonly ConsoleOutputInterface $output
    )
    {
    }

    public function isSuitable(ConsoleInputInterface $input): bool
    {
        return $input->hasOption('--interactive') === true || $input->hasOption('-na') === true;
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
        $this->output->stdout("$prompt" . PHP_EOL);

        $result = trim(fgets(STDIN));

        return is_numeric($result) ? (int)$result : $result;
    }

    private function askForApproval(ConsoleInputInterface $input, string $key, array $value): void
    {
        $this->output->stdout("Применить опцию $key? ({$value["description"]}) [да] да/нет" . PHP_EOL);
        $approval = trim(fgets(STDIN));

        if ($approval === '' || $approval === 'да') {
            $input->options[] = $key;
        }
    }
}
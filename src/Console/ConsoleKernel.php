<?php

namespace Alpha\Console;

use Alpha\Components\ConsoleInput\ConsoleInput;
use Alpha\Contracts\{
    ConsoleCommandInterface,
    ConsoleKernelInterface,
};

class ConsoleKernel implements ConsoleKernelInterface
{
    private array $commandMap;

    public function __construct()
    {
    }

    public function handle(array $argv): void
    {
        $this->dispatch($argv);
    }

    public function addCommandsNamespaces(array $commandsNamespaces): void
    {
        foreach ($commandsNamespaces as $commandsNamespace) {
            foreach (glob("{$commandsNamespace}/*.php") as $class) {
                $this->addCommand($class);
            }
        }
    }

    public function addCommand(string $className): void
    {
        $namespaceName = str_replace(['/', '\app\\'], ['\\', ''], strstr($className, '.php', true));

        if (is_subclass_of($namespaceName, ConsoleCommandInterface::class) === false) {
            throw new \InvalidArgumentException("Неверный тип объекта $namespaceName");
        }

        $name = explode(' ', call_user_func([$namespaceName, 'getName']))[0];
        $description = call_user_func([$namespaceName, 'getDescription']);
        $isHidden = call_user_func([$namespaceName, 'isHidden']);

        $this->commandMap[$name] = [
            'namespace' => $namespaceName,
            'description' => $description,
            'isHidden' => $isHidden,
        ];
    }

    public function dispatch(array $argv): void
    {
        $command = $argv[1] ?? 'info';

        $params = array_slice($argv, 2);

        $handler = $this->commandMap[$command]['namespace']
            ?? throw new \Exception("Команда $command не найдена", 404);

        $paramsString = explode(' ', call_user_func([$handler, 'getName']))[1] ?? '';

        $args = $this->mapArgs($params, $paramsString);

        $handler = container()->build($handler);

        $this->addArgumentsInInput($handler->input, $params, $paramsString);

        if (in_array('-h', $argv)) {
            $handler->getCommandInfo($this->prepareArgs($paramsString));
            return;
        }

        $handler->execute(...$args);

    }

    public function getCommandMap()
    {
        return $this->commandMap;
    }

    private function mapArgs(array $args, string $paramString): array
    {
        $arguments = [];
        $preparedArgs = $this->prepareArgs($paramString);

        $givenParams = $this->parseGivenParams($args, $preparedArgs);

        foreach ($preparedArgs as $paramName => $paramProperties) {
            $isExists = in_array($paramName, array_keys($givenParams));

            if ($paramProperties['isRequired'] === true && $isExists === false) {
                throw new \InvalidArgumentException('отсутствуют обязательные аргументы: ' . $paramName);
            }

            if ($isExists === true) {
                $arguments[] = $givenParams[$paramName];
                continue;
            }

            if ($paramProperties['defaultValue'] !== null) {
                $arguments[] = $paramProperties['defaultValue'];
            }
        }

        return $arguments;
    }

    private function parseGivenParams(array $args, array $preparedArgs): array
    {
        $arguments = [];
        $listKeys = array_keys($preparedArgs);

        foreach ($args as $key => $value) {
            $paramName = $listKeys[$key];

            $arguments[$paramName] = $value;
        }

        return $arguments;
    }

    private function prepareArgs(string $paramString): array
    {
        $resultParams = [];

        preg_match_all('/\{([^}]*)\}/', $paramString, $params);

        foreach ($params[1] as $param) {
            $isRequired = true;

            if (str_starts_with($param, '?')) {
                $param = substr($param, 1);
                $isRequired = false;
            }

            $defaultValue = null;

            if (substr_count($param, "=") === 1 && str_ends_with($param, "=") === false) {
                $arr = explode("=", $param);
                $defaultValue = $arr[1];
                $param = $arr[0];
            }

            $resultParams[$param] = [
                'isRequired' => $isRequired,
                'defaultValue' => $defaultValue,
            ];
        }

        return $resultParams;
    }

    public function addArgumentsInInput(ConsoleInput $input, array $argv, string $paramString): void
    {
        $input->setArguments($this->prepareArgs($paramString));
        $input->removeArgumentStartingWithDoubleDash();

        foreach ($argv as $argument) {
            if (str_contains($argument, '--')) {
                $input->options[str_replace('--', '', $argument)] = $argument;
                unset($input->arguments[$argument]);
            }
        }

        $tmpArgv = [];

        foreach ($argv as $argument) {
            $tmpArgv[] = $argument;
        }

        $input->arguments = $this->combineArrays(array_keys($input->arguments), $tmpArgv);
    }

    private function combineArrays($keys, $values): array
    {
        $combinedArray = array();
        $minLength = min(count($keys), count($values));

        for ($i = 0; $i < $minLength; $i++) {
            $combinedArray[$keys[$i]] = $values[$i];
        }

        return $combinedArray;
    }
}

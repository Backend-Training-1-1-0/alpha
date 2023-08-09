<?php

namespace Alpha\Console;

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
        $namespaceName = str_replace(['/', '\app\\', 'vendor\\efko-cr\\alpha\\src\\'], ['\\', '', 'Alpha\\'], strstr($className, '.php', true));

        if (is_subclass_of(ucfirst($namespaceName), ConsoleCommandInterface::class) === false) {
            throw new \InvalidArgumentException("Неверный тип объекта $namespaceName");
        }

        $name = explode(' ', call_user_func([$namespaceName, 'getSignature']))[0];
        $description = call_user_func([$namespaceName, 'getDescription']);
        $isHidden = call_user_func([$namespaceName, 'getHidden']);

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

        $handler = container()->build($handler);

        $handler->execute();
    }

    public function getCommandMap()
    {
        return $this->commandMap;
    }
}

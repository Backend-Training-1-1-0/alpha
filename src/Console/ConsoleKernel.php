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
        $namespaceName = $this->getNamespaceFromFile($className);

        if (is_subclass_of($namespaceName, ConsoleCommandInterface::class) === false) {
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

    public function getCommandMap(): array
    {
        return $this->commandMap;
    }

    public function terminate(): void
    {
        echo 'Завершение скрипта';

        exit;
    }

    private function getNamespaceFromFile(string $filePath): string
    {
        $fileContent = file_get_contents($filePath);
        $matches = [];

        preg_match('/^namespace\s+(.+?);/m', $fileContent, $matches);
        $namespace = $matches[1] ?? '';

        preg_match('/^class\s+(\w+)/m', $fileContent, $matches);
        $className = $matches[1] ?? '';

        return trim($namespace . '\\' . $className, '\\');
    }

}

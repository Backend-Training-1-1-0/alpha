<?php

namespace Alpha\Contracts;

interface DIContainerInterface
{
    static function getInstance(array $config = []): self;
    function build(string $className): object;
    function make(string $interfaceName): object;
    function register(string $contract, object $dependence): void;
    function call(string|callable $handler, string|null $method = null, array $defaultArgs = []): mixed;
}

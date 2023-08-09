<?php

namespace Contracts;

interface ConsoleOutputInterface
{
     function __construct();
     function stdout(string $result): void;
}

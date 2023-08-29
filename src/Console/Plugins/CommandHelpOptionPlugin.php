<?php

namespace Alpha\Console\Plugins;

use Alpha\Console\Components\CommandInfoService;
use Alpha\Console\ConsoleKernel;
use Alpha\Contracts\ConsoleInputInterface;
use Alpha\Contracts\ConsoleInputPluginInterface;

class CommandHelpOptionPlugin implements ConsoleInputPluginInterface
{
    public function __construct()
    {
    }

    function isSuitable(ConsoleInputInterface $input): bool
    {
        return $input->hasOption('--help') === true || $input->hasOption('--h') === true;
    }

    function handle(ConsoleInputInterface $input): void
    {
        /** @var CommandInfoService $infoService */
        $infoService = container()->build(CommandInfoService::class);
        $infoService->setDefinition($input->getDefinition());
        $infoService->printCommandInfo();

        container()->call(ConsoleKernel::class, 'terminate');
    }
}
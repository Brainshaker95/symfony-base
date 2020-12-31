<?php

namespace App\Traits;

use App\Service\CommandService;

trait HasCommandService
{
    /**
     * @var CommandService;
     */
    private $commandService;

    /**
     * @required
     */
    public function setCommandService(CommandService $commandService): void
    {
        $this->commandService = $commandService;
    }
}

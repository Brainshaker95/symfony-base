<?php

namespace App\Traits;

use Psr\Log\LoggerInterface;

trait HasLogger
{
    /**
     * @var LoggerInterface;
     */
    private $logger;

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}

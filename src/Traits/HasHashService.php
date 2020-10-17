<?php

namespace App\Traits;

use App\Service\HashService;

trait HasHashService
{
    /**
     * @var HashService;
     */
    private $hashService;

    /**
     * @required
     */
    public function setHashService(HashService $hashService): void
    {
        $this->hashService = $hashService;
    }
}

<?php

namespace App\Traits;

use Liip\ImagineBundle\Service\FilterService;

trait HasFilterService
{
    /**
     * @var FilterService;
     */
    private $filterService;

    /**
     * @required
     */
    public function setFilterService(FilterService $filterService): void
    {
        $this->filterService = $filterService;
    }
}

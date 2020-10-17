<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\RequestStack;

trait HasRequestStack
{
    /**
     * @var RequestStack;
     */
    private $requestStack;

    /**
     * @required
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }
}

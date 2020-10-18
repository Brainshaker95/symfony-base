<?php

namespace App\Traits;

use Symfony\Component\Routing\RouterInterface;

trait HasRouter
{
    /**
     * @var RouterInterface;
     */
    private $router;

    /**
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
}

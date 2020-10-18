<?php

namespace App\Traits;

use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

trait HasGuardHandler
{
    /**
     * @var GuardAuthenticatorHandler
     */
    private $guardHandler;

    /**
     * @required
     */
    public function setGuardHandler(GuardAuthenticatorHandler $guardHandler): void
    {
        $this->guardHandler = $guardHandler;
    }
}

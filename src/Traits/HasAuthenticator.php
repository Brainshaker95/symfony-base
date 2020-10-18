<?php

namespace App\Traits;

use App\Security\LoginFormAuthenticator;

trait HasAuthenticator
{
    /**
     * @var LoginFormAuthenticator
     */
    private $authenticator;

    /**
     * @required
     */
    public function setAuthenticator(LoginFormAuthenticator $authenticator): void
    {
        $this->authenticator = $authenticator;
    }
}

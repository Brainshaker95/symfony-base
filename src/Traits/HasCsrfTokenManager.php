<?php

namespace App\Traits;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

trait HasCsrfTokenManager
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @required
     */
    public function setCsrfTokenManager(CsrfTokenManagerInterface $csrfTokenManager): void
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }
}

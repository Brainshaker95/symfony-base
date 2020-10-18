<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

trait HasSession
{
    /**
     * @var Session<mixed>
     */
    private $session;

    /**
     * @required
     *
     * @param Session<mixed> $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }
}

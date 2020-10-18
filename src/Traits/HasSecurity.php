<?php

namespace App\Traits;

use Symfony\Component\Security\Core\Security;

trait HasSecurity
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @required
     */
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }
}

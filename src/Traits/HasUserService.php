<?php

namespace App\Traits;

use App\Service\UserService;

trait HasUserService
{
    /**
     * @var UserService;
     */
    private $userService;

    /**
     * @required
     */
    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }
}

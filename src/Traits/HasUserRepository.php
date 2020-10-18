<?php

namespace App\Traits;

use App\Repository\UserRepository;

trait HasUserRepository
{
    /**
     * @var UserRepository;
     */
    private $userRepository;

    /**
     * @required
     */
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }
}

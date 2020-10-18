<?php

namespace App\Traits;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

trait HasPasswordEncoder
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @required
     */
    public function setPasswordEncoder(UserPasswordEncoderInterface $passwordEncoder): void
    {
        $this->passwordEncoder = $passwordEncoder;
    }
}

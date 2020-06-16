<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\UserService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UserExtension extends AbstractExtension
{
    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('can_modify', [$this, 'canModify']),
        ];
    }

    public function canModify(User $curentUser, User $userToCheck): bool
    {
        return $this->userService->canModify($curentUser, $userToCheck);
    }
}

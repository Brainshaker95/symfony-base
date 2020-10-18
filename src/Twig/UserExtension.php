<?php

namespace App\Twig;

use App\Entity\User;
use App\Traits\HasUserService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    use HasUserService;

    /**
     * @return array<TwigFilter>
     */
    public function getFilters()
    {
        return [
            new TwigFilter('can_modify', [$this, 'canModify']),
        ];
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('clear_user_files_from_tmp', [$this, 'clearUserFilesFromTmp']),
        ];
    }

    public function canModify(User $curentUser, User $userToCheck): bool
    {
        return $this->userService->canModify($curentUser, $userToCheck);
    }

    public function clearUserFilesFromTmp(?User $user = null): void
    {
        $this->userService->clearUserFilesFromTmp($user);
    }
}

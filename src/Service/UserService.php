<?php

namespace App\Service;

use App\Entity\User;

class UserService
{
    public function canModify(User $curentUser, User $userToCheck): bool
    {
        $userToCheckRoles = $userToCheck->getRoles();
        $currentUserRoles = $curentUser->getRoles();

        if ($curentUser === $userToCheck
            || (
                !in_array('ROLE_SUPER_ADMIN', $currentUserRoles)
                && in_array('ROLE_ADMIN', $userToCheckRoles))
            ) {
            return false;
        }

        $successCounter = 0;

        foreach ($userToCheckRoles as $userToCheckRole) {
            if (in_array($userToCheckRole, $currentUserRoles)) {
                $successCounter += 1;
            }
        }

        return $successCounter >= count($userToCheckRoles);
    }
}

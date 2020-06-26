<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Finder\Finder;

class UserService
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * @var HashService
     */
    protected $hashService;

    public function __construct(FileService $fileService, HashService $hashService)
    {
        $this->fileService = $fileService;
        $this->hashService = $hashService;
    }

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

    public function clearUserFilesFromTmp(?User $user = null): void
    {
        if (!$user) {
            return;
        }

        $tmpFolder = __DIR__ . '..\\..\\..\\public\\uploads\\tmp';

        if (!is_dir($tmpFolder)) {
            return;
        }

        $finder        = new Finder();
        $encodedUserId = $this->hashService->encode($user->getId());

        foreach ($finder->files()->in($tmpFolder) as $file) {
            $filename = $file->getRelativePathname();

            if (strpos($filename, $encodedUserId) === 0) {
                $this->fileService->delete($filename, 'tmp');
            }
        }
    }
}

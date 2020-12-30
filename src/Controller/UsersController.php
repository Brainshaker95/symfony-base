<?php

namespace App\Controller;

use App\Entity\User;
use App\Traits\HasEntityManager;
use App\Traits\HasFileService;
use App\Traits\HasHashService;
use App\Traits\HasLogger;
use App\Traits\HasUserRepository;
use App\Traits\HasUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends FrontendController
{
    use HasEntityManager;
    use HasFileService;
    use HasHashService;
    use HasLogger;
    use HasUserRepository;
    use HasUserService;

    public function deleteUserAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $this->hashService->decode($request->get('id', 0));

        /**
         * @var User|null
         */
        $user = $this->userRepository->find($id);

        /**
         * @var User|null
         */
        $currentUser = $this->getUser();

        if (!$currentUser || !$user || !$this->userService->canModify($currentUser, $user)) {
            return $this->forbidden();
        }

        $this->deleteProfileImageFile($user);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->logger->info(
            'User "' .
            $currentUser->getUsername() .
            '" has just deleted user "' .
            $user->getUsername() .
            '"'
        );

        return $this->json([
            'success' => true,
        ]);
    }

    public function deleteProfileImageAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $this->hashService->decode($request->get('id', 0));

        /**
         * @var User|null
         */
        $user = $this->userRepository->find($id);

        /**
         * @var User|null
         */
        $currentUser = $this->getUser();

        if (!$currentUser || !$user || !$this->userService->canModify($currentUser, $user)) {
            return $this->forbidden();
        }

        $this->deleteProfileImageFile($user);

        $this->logger->info(
            'User "' .
            $currentUser->getUsername() .
            '" has just deleted the profile image of user "' .
            $user->getUsername() .
            '"'
        );

        return $this->json([
            'success' => true,
        ]);
    }

    public function updateRolesAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id    = $this->hashService->decode($request->get('id', 0));
        $roles = $request->get('roles', []);

        /**
         * @var User|null
         */
        $user = $this->userRepository->find($id);

        /**
         * @var User|null
         */
        $currentUser = $this->getUser();

        if (!$currentUser || !$user || !$this->userService->canModify($currentUser, $user)) {
            return $this->forbidden();
        }

        if (in_array('ROLE_SUPER_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_SUPER_ADMIN']);
        }

        $oldRoles = $user->getRoles();

        $user->setRoles($roles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->info(
            'User "' .
            $currentUser->getUsername() .
            '" has just updated the roles of user"' .
            $user->getUsername() .
            '". (' .
            json_encode($oldRoles) .
            ' => ' .
            json_encode($user->getRoles()) .
            ')'
        );

        return $this->json([
            'success' => true,
        ]);
    }

    private function deleteProfileImageFile(User $user): void
    {
        $image = $user->getImage();

        if ($image && $this->fileService->delete($image->getFilename(), 'profile_images')) {
            $asset = $image->getAsset();

            $user->setImage(null);
            $this->entityManager->persist($user);
            $this->entityManager->remove($image);

            if ($asset) {
                $this->entityManager->remove($asset);
            }

            $this->entityManager->flush();
        }
    }
}

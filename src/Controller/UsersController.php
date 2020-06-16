<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use App\Service\FileService;
use App\Service\HashService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var FileService;
     */
    protected $fileService;

    /**
     * @var HashService;
     */
    protected $hashService;

    /**
     * @var ImageRepository;
     */
    protected $imageRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UserRepository;
     */
    protected $userRepository;

    /**
     * @var UserService
     */
    protected $userService;

    public function __construct(
        EntityManagerInterface $entityManager,
        FileService $fileService,
        HashService $hashService,
        ImageRepository $imageRepository,
        LoggerInterface $logger,
        UserRepository $userRepository,
        UserService $userService
    ) {
        $this->entityManager   = $entityManager;
        $this->fileService     = $fileService;
        $this->hashService     = $hashService;
        $this->imageRepository = $imageRepository;
        $this->logger          = $logger;
        $this->userRepository  = $userRepository;
        $this->userService     = $userService;
    }

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
            return $this->json([
                'success' => false,
            ]);
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
            return $this->json([
                'success' => false,
            ]);
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
            return $this->json([
                'success' => false,
            ]);
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
            $user->setImage(null);
            $this->entityManager->persist($user);
            $this->entityManager->remove($image);
            $this->entityManager->flush();
        }
    }
}

<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\HashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var HashService;
     */
    protected $hashService;

    /**
     * @var UserRepository;
     */
    protected $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        HashService $hashService,
        UserRepository $userRepository
    ) {
        $this->entityManager  = $entityManager;
        $this->hashService    = $hashService;
        $this->userRepository = $userRepository;
    }

    public function adminAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('page/admin/dashboard.html.twig');
    }

    public function usersAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $this->userRepository->findAll();

        return $this->render('page/admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    public function deleteUserAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $id = $this->hashService->decode($request->get('id', 0));

        /**
         * @var UserInterface|null
         */
        $user = $this->userRepository->find($id);

        if (!$user || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return $this->json([
                'success' => false,
            ]);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends FrontendController
{
    /**
     * @var array<string>
     */
    protected static $roles = [
        'ROLE_ADMIN',
        'ROLE_USER',
    ];

    /**
     * @var UserRepository;
     */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function adminAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('page/admin/dashboard.html.twig');
    }

    public function usersAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('page/admin/users.html.twig', [
            'users' => $this->userRepository->findAll(),
            'roles' => self::$roles,
        ]);
    }
}

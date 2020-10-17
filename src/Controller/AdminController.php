<?php

namespace App\Controller;

use App\Traits\HasUserRepository;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends FrontendController
{
    use HasUserRepository;

    /**
     * @var array<string>
     */
    protected const ROLES = [
        'ROLE_ADMIN',
        'ROLE_USER',
    ];

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
            'roles' => self::ROLES,
        ]);
    }
}

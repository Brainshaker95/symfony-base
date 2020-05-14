<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function indexAction(): Response
    {
        return $this->render('page/index.html.twig');
    }

    public function profileAction(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('page/profile.html.twig', [
            'user' => $user,
        ]);
    }
}

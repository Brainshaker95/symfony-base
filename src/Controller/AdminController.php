<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    public function adminAction(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('page/index.html.twig');
    }
}

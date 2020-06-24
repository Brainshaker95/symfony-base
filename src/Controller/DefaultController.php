<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function redirectToIndexAction(Request $request): RedirectResponse
    {
        return $this->redirectToRoute('app_index', [
            '_locale' => $request->getLocale(),
        ]);
    }

    public function indexAction(): Response
    {
        return $this->render('page/index.html.twig');
    }
}

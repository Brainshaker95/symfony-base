<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends FrontendController
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

    public function imprintAction(): Response
    {
        return $this->render('page/imprint.html.twig');
    }

    public function privacyAction(): Response
    {
        return $this->render('page/privacy.html.twig');
    }
}

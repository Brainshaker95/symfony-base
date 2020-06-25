<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class FrontendController extends AbstractController
{
    public function renderError(?Response $response = null): Response
    {
        $response = $response ? $response : $this->internalError();

        return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
            'status_code' => $response->getStatusCode(),
        ], $response);
    }

    public function renderForbidden(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], $this->forbidden());
    }

    public function renderNotFound(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], $this->notFound());
    }

    public function internalError(): Response
    {
        return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function forbidden(): Response
    {
        return new Response('', Response::HTTP_FORBIDDEN);
    }

    public function methodNotAllowed(): Response
    {
        return new Response('', Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function noContent(): Response
    {
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function notFound(): Response
    {
        return new Response('', Response::HTTP_NOT_FOUND);
    }

    public function unprocessable(): Response
    {
        return new Response('', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

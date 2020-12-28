<?php

namespace App\Controller;

use App\Form\Type\NewsArticleType;
use App\Traits\HasEntityManager;
use App\Traits\HasNewsArticleRepository;
use App\Traits\HasUserRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends FrontendController
{
    use HasEntityManager;
    use HasNewsArticleRepository;
    use HasUserRepository;

    private const ROLES = [
        'ROLE_ADMIN',
        'ROLE_USER',
    ];

    public function adminAction(): Response
    {
        return $this->render('page/admin/dashboard.html.twig');
    }

    public function newsAction(Request $request): Response
    {
        /**
         * @var Form
         */
        $form        = $this->createForm(NewsArticleType::class);
        $handledForm = $form->handleRequest($request);
        $hasErrors   = false;

        if ($form->getErrors(true)->count()) {
            $hasErrors = true;
        }

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $newsArticle     = $handledForm->getData();
            $existingArticle = $this->newsArticleRepository->findBy(['title' => $newsArticle->getTitle()]);

            $this->addFlash('_params', serialize([
                '{{ title }}' => $newsArticle->getTitle(),
            ]));

            if (!$existingArticle) {
                $this->entityManager->persist($newsArticle);
                $this->entityManager->flush();

                $this->addFlash('success', 'page.news.success.article_added');

                return $this->redirect($request->getUri());
            } else {
                $this->addFlash('error', 'page.news.error.duplicate_title');
            }
        }

        return $this->render('page/admin/news.html.twig', [
            'news_articles'     => $this->newsArticleRepository->findBy([], ['createdAt' => 'DESC']),
            'news_article_form' => $form->createView(),
            'has_errors'        => $hasErrors,
        ]);
    }

    public function usersAction(): Response
    {
        return $this->render('page/admin/users.html.twig', [
            'users' => $this->userRepository->findAll(),
            'roles' => self::ROLES,
        ]);
    }
}

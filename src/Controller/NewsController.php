<?php

namespace App\Controller;

use App\Entity\NewsArticle;
use App\Entity\User;
use App\Form\Type\NewsArticleType;
use App\Traits\HasEntityManager;
use App\Traits\HasHashService;
use App\Traits\HasLogger;
use App\Traits\HasNewsArticleRepository;
use App\Traits\HasTranslator;
use Carbon\Carbon;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends FrontendController
{
    use HasEntityManager;
    use HasHashService;
    use HasLogger;
    use HasNewsArticleRepository;
    use HasTranslator;

    private const PAGE_SIZE = 15;

    public function newsAction(Request $request): Response
    {
        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $page = (int) $request->get('page', 1);

        if ($page < 1) {
            return $this->renderNotFound();
        }

        $limit      = self::PAGE_SIZE;
        $paginator  = $this->newsArticleRepository->getNewsArticlePaginator($page, $limit);
        $totalPages = (int) ceil($paginator->count() / $limit);

        if ($page > $totalPages && $page !== 1) {
            return $this->renderNotFound();
        }

        return $this->render('page/news.html.twig', [
            'paginator'    => $paginator,
            'current_page' => $page,
            'total_pages'  => $totalPages,
        ]);
    }

    /**
     * @return Response|JsonResponse
     */
    public function pagingAction(Request $request)
    {
        /**
         * @var User|null
         */
        $user         = $this->getUser();
        $newsArticles = [];

        if ($user) {
            $limit = self::PAGE_SIZE;
            $page  = (int) $request->get('page', 1);

            if ($page < 1) {
                return $this->notFound();
            }

            $paginator  = $this->newsArticleRepository->getNewsArticlePaginator($page, $limit);
            $totalPages = (int) ceil($paginator->count() / $limit);

            if ($page > $totalPages) {
                return $this->notFound();
            }

            foreach ($paginator as $newsArticle) {
                $createdAt = $this->getFormattedDate($newsArticle->getCreatedAt());
                $updatedAt = $this->getFormattedDate($newsArticle->getUpdatedAt());
                $author    = $newsArticle->getAuthor();

                $newsArticles[] = [
                    'created-at'  => $createdAt,
                    'updated-at'  => $updatedAt,
                    'was-updated' => $createdAt !== $updatedAt,
                    'title'       => $newsArticle->getTitle(),
                    'text'        => nl2br(strip_tags($newsArticle->getText() ?: '', '<p><a>')),
                    'author'      => $author ? '– ' . $author->getUsername() : '',
                ];
            }
        }

        return $this->json([
            'success'      => count($newsArticles) > 0,
            'newsArticles' => $newsArticles,
        ]);
    }

    public function deleteNewsArticleAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $this->hashService->decode($request->get('id', 0));

        /**
         * @var NewsArticle|null
         */
        $newsArticle = $this->newsArticleRepository->find($id);

        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$newsArticle || !$user) {
            return $this->unprocessable();
        }

        $this->entityManager->remove($newsArticle);
        $this->entityManager->flush();

        $this->logger->info(
            'User "' .
            $user->getUsername() .
            '" has just deleted news article "' .
            $newsArticle->getTitle() .
            '"'
        );

        return $this->json([
            'success' => true,
        ]);
    }

    public function editNewsArticleAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $id = $this->hashService->decode($request->get('id', 0));

        /**
         * @var NewsArticle|null
         */
        $newsArticle = $this->newsArticleRepository->find($id);

        /**
         * @var User|null
         */
        $user = $this->getUser();

        if (!$user) {
            return $this->forbidden();
        }

        if (!$newsArticle) {
            return $this->unprocessable();
        }

        /**
         * @var Form
         */
        $form = $this->createForm(NewsArticleType::class, $newsArticle);

        if ($request->isMethod('GET')) {
            return $this->render('page/admin/edit-news-form.html.twig', [
                'edit_news_form' => $form->createView(),
            ]);
        }

        $form->handleRequest($request);

        $success = false;

        if ($form->isSubmitted()
            && $form->get('title')->getData()
            && $form->get('text')->getData()) {
            $this->entityManager->persist($newsArticle);
            $this->entityManager->flush();
            $success = true;

            $this->logger->info(
                'User "' .
                $user->getUsername() .
                '" has just edited news article "' .
                $newsArticle->getTitle() .
                '"'
            );
        }

        return $this->json([
            'success' => $success,
            'title'   => $newsArticle->getTitle(),
        ]);
    }

    private function getFormattedDate(Carbon $date): string
    {
        return date_format($date, $this->translator->trans('page.news.datetime_format'));
    }
}

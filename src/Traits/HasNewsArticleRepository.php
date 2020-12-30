<?php

namespace App\Traits;

use App\Repository\NewsArticleRepository;

trait HasNewsArticleRepository
{
    /**
     * @var NewsArticleRepository;
     */
    private $newsArticleRepository;

    /**
     * @required
     */
    public function setNewsArticleRepository(NewsArticleRepository $newsArticleRepository): void
    {
        $this->newsArticleRepository = $newsArticleRepository;
    }
}

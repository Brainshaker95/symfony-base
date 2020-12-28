<?php

namespace App\Repository;

use App\Entity\NewsArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class NewsArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsArticle::class);
    }

    /**
     * @return Paginator<NewsArticle>
     */
    public function getNewsArticlePaginator(int $page, int $limit)
    {
        $query = $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery();

        $paginator = new Paginator($query);
        $offset    = $limit * ($page - 1);

        $paginator
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $paginator;
    }
}

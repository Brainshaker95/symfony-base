<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @return Paginator<Image>
     */
    public function getGalleryPaginator(int $page, int $limit)
    {
        $query = $this->createQueryBuilder('i')
            ->where('i.type = \'gallery\'')
            ->orderBy('i.id', 'DESC')
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

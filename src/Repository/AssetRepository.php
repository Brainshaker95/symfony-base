<?php

namespace App\Repository;

use App\Entity\Asset\Asset;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class AssetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asset::class);
    }

    /**
     * @return Paginator<Asset>
     */
    public function getGalleryPaginator(int $page, int $limit)
    {
        $query = $this->createQueryBuilder('a')
            ->leftJoin('a.image', 'i')
            ->leftJoin('a.video', 'v')
            ->where('i.type = \'gallery\' OR v.type = \'gallery\'')
            ->orderBy('a.createdAt', 'DESC')
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

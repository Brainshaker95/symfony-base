<?php

namespace App\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait HasEntityManager
{
    /**
     * @var EntityManagerInterface;
     */
    private $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}

<?php

namespace App\Traits;

use App\Repository\ImageRepository;

trait HasImageRepository
{
    /**
     * @var ImageRepository;
     */
    private $imageRepository;

    /**
     * @required
     */
    public function setImageRepository(ImageRepository $imageRepository): void
    {
        $this->imageRepository = $imageRepository;
    }
}

<?php

namespace App\Traits;

use App\Repository\VideoRepository;

trait HasVideoRepository
{
    /**
     * @var VideoRepository;
     */
    private $videoRepository;

    /**
     * @required
     */
    public function setVideoRepository(VideoRepository $videoRepository): void
    {
        $this->videoRepository = $videoRepository;
    }
}

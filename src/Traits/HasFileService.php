<?php

namespace App\Traits;

use App\Service\FileService;

trait HasFileService
{
    /**
     * @var FileService;
     */
    private $fileService;

    /**
     * @required
     */
    public function setFileService(FileService $fileService): void
    {
        $this->fileService = $fileService;
    }
}

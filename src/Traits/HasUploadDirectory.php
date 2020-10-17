<?php

namespace App\Traits;

trait HasUploadDirectory
{
    /**
     * @var string;
     */
    private $uploadDirectory;

    /**
     * @required
     */
    public function setUploadDirectory(string $uploadDirectory): void
    {
        $this->uploadDirectory = $uploadDirectory;
    }
}

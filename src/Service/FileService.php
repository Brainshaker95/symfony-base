<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    /**
     * @var string
     */
    protected $uploadDirectory;

    /**
     * @var SluggerInterface
     */
    protected $slugger;

    public function __construct($uploadDirectory, SluggerInterface $slugger)
    {
        $this->uploadDirectory = $uploadDirectory;
        $this->slugger         = $slugger;
    }

    public function upload(UploadedFile $file, string $folder = '')
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename     = $this->slugger->slug($originalFilename);
        $fileName         = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        $targetDirectory  = $this->uploadDirectory;

        if ($folder) {
            $targetDirectory .= '/' . $folder;
        }

        try {
            $file->move($targetDirectory, $fileName);

            return $fileName;
        } catch (FileException $e) {
            return null;
        }
    }
}

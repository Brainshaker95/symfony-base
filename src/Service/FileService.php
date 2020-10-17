<?php

namespace App\Service;

use App\Traits\HasSlugger;
use App\Traits\HasUploadDirectory;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    use HasSlugger;
    use HasUploadDirectory;

    public function upload(UploadedFile $file, string $folder = '', string $filePrefix = ''): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName() ?: '', PATHINFO_FILENAME);
        $safeFilename     = $this->slugger->slug($originalFilename);
        $extension        = $file->guessExtension() ?: 'file';
        $filename         = $filePrefix . $safeFilename . '-' . uniqid() . '.' . $extension;
        $targetDirectory  = $this->uploadDirectory;

        if ($folder) {
            $targetDirectory .= '/' . $folder;
        }

        try {
            $file->move($targetDirectory, $filename);

            return $filename;
        } catch (FileException $e) {
            return null;
        }
    }

    public function delete(string $filename, string $folder = ''): bool
    {
        $targetDirectory = $this->uploadDirectory;

        if ($folder) {
            $targetDirectory .= '/' . $folder;
        }

        try {
            unlink($targetDirectory . '/' . $filename);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public function move(string $filename, string $folder = '', string $newFilename = '', string $targetDirectory = ''): bool
    {
        $currentDirectory = $this->uploadDirectory;
        $targetDirectory  = $this->uploadDirectory . '/' . $targetDirectory;

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory);
        }

        if ($folder) {
            $currentDirectory .= '/' . $folder;
        }

        if (!$newFilename) {
            $newFilename = $filename;
        }

        try {
            rename(
                $currentDirectory . '/' . $filename,
                $targetDirectory . '/' . $newFilename
            );
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}

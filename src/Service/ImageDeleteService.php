<?php

namespace App\Service;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class ImageDeleteService
{

    private string $publicDir;

    public function __construct(string $publicDir)
    {
        $this->publicDir = $publicDir;
    }

    public function deleteImage($object)
    {
        $filesystem = new Filesystem();
        $filePath = $this->publicDir . $object;

        if (file_exists($filePath) && !is_dir($filePath)) {
            try {
                $filesystem->remove($filePath);
            } catch (IOException $ex) {
                throw new IOException($ex->getMessage());
            }
        }
    }
}
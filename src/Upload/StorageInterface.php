<?php

namespace App\Upload;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageInterface
{
    public function remove($entity): bool;

    public function resolvePath($entity): ?string;

    public function resolveUri($entity): ?string;

    public function upload(UploadedFile $file, $entity): File;
}

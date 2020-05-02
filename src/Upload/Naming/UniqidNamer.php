<?php

namespace App\Upload\Naming;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UniqidNamer implements NamerInterface
{
    public function name(UploadedFile $file): string
    {
        $name = str_replace('.', '', uniqid('', true));
        $extension = $this->getExtension($file);

        if (is_string($extension) && '' !== $extension) {
            $name = sprintf('%s.%s', $name, $extension);
        }

        return $name;
    }

    private function getExtension(UploadedFile $file): ?string
    {
        $originalName = $file->getClientOriginalName();

        if ('' !== ($extension = pathinfo($originalName, PATHINFO_EXTENSION))) {
            return $extension;
        }

        if ('' !== ($extension = $file->guessExtension())) {
            return $extension;
        }

        return null;
    }
}

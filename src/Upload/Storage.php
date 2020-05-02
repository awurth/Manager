<?php

namespace App\Upload;

use App\Upload\Mapping\Mapping;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Storage implements StorageInterface
{
    private $mapping;
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor, Mapping $mapping)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->mapping = $mapping;
    }

    public function remove($entity): bool
    {
        $file = $this->resolvePath($entity);

        return file_exists($file) ? unlink($file) : false;
    }

    public function upload(UploadedFile $file, $entity): File
    {
        if (!$this->mapping->supports($entity)) {
            throw new InvalidArgumentException('Not supported entity');
        }

        $name = $this->mapping->getUploadName($file);

        $movedFile = $file->move($this->mapping->getUploadDestination(), $name);

        $this->remove($entity);

        $this->propertyAccessor->setValue($entity, $this->mapping->getPropertyPath(), $name);

        return $movedFile;
    }

    public function resolveUri($entity): ?string
    {
        if (!$this->mapping->supports($entity)) {
            throw new InvalidArgumentException('Not supported entity');
        }

        $filename = $this->getFilename($entity);

        if (!$filename) {
            return null;
        }

        return $this->mapping->getUriPrefix().'/'.$filename;
    }

    public function resolvePath($entity): ?string
    {
        if (!$this->mapping->supports($entity)) {
            throw new InvalidArgumentException('Not supported entity');
        }

        $filename = $this->getFilename($entity);

        if (!$filename) {
            return null;
        }

        return $this->mapping->getUploadDestination().DIRECTORY_SEPARATOR.$filename;
    }

    private function getFilename($entity): ?string
    {
        return $this->propertyAccessor->getValue($entity, $this->mapping->getPropertyPath());
    }
}

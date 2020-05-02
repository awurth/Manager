<?php

namespace App\Upload\Mapping;

use App\Upload\Naming\NamerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Mapping
{
    private $class;
    private $namer;
    private $propertyPath;
    private $uploadDestination;
    private $uriPrefix;

    public function __construct(NamerInterface $namer, string $class, string $uploadDestination, string $uriPrefix, string $propertyPath)
    {
        $this->namer = $namer;
        $this->class = $class;
        $this->uploadDestination = $uploadDestination;
        $this->uriPrefix = $uriPrefix;
        $this->propertyPath = $propertyPath;
    }

    public function supports($entity): bool
    {
        return $entity instanceof $this->class;
    }

    public function getUploadName(UploadedFile $file): string
    {
        return $this->getNamer()->name($file);
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    public function setPropertyPath(string $propertyPath): void
    {
        $this->propertyPath = $propertyPath;
    }

    public function getUploadDestination(): string
    {
        return $this->uploadDestination;
    }

    public function setUploadDestination(string $uploadDestination): void
    {
        $this->uploadDestination = $uploadDestination;
    }

    public function getUriPrefix(): string
    {
        return $this->uriPrefix;
    }

    public function setUriPrefix(string $uriPrefix): void
    {
        $this->uriPrefix = $uriPrefix;
    }

    public function getNamer(): NamerInterface
    {
        return $this->namer;
    }

    public function setNamer(NamerInterface $namer): void
    {
        $this->namer = $namer;
    }
}

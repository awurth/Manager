<?php

namespace App\Form\Model;

use App\Validator\UniqueProjectSlug;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueProjectSlug()
 */
class CreateProject
{
    /**
     * @Assert\NotBlank()
     */
    public $projectGroup;

    public $type;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[0-9a-z-]+$/")
     */
    public $slug;

    /**
     * @Assert\NotBlank()
     */
    public $name;

    public $description;

    /**
     * @var UploadedFile|File
     *
     * @Assert\Image()
     */
    public $logoFile;
}

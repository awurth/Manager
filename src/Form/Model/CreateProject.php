<?php

namespace App\Form\Model;

use App\Validator\UniqueProjectSlug;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable()
 *
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

    public $imageFilename;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="project_image", fileNameProperty="imageFilename")
     *
     * @Assert\Image()
     */
    public $imageFile;
}

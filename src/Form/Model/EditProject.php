<?php

namespace App\Form\Model;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable()
 */
class EditProject
{
    public $type;

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

    public function __construct(Project $project)
    {
        $this->type = $project->getType();
        $this->name = $project->getName();
        $this->description = $project->getDescription();
    }
}

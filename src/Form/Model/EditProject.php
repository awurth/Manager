<?php

namespace App\Form\Model;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EditProject
{
    public $type;

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

    public function __construct(Project $project)
    {
        $this->type = $project->getType();
        $this->name = $project->getName();
        $this->description = $project->getDescription();
    }
}

<?php

namespace App\Form\Model;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class EditProject
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    public $description;

    /**
     * @var UploadedFile|null
     *
     * @Assert\Image()
     */
    public $logoFile;

    public function __construct(Project $project)
    {
        $this->name = $project->getName();
        $this->description = $project->getDescription();
    }
}

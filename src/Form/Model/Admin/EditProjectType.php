<?php

namespace App\Form\Model\Admin;

use App\Entity\ProjectType;
use Symfony\Component\Validator\Constraints as Assert;

class EditProjectType
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    public function __construct(ProjectType $projectType)
    {
        $this->name = $projectType->getName();
    }
}

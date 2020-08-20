<?php

namespace App\Form\Model;

use App\Entity\ProjectGroup;
use Symfony\Component\Validator\Constraints as Assert;

final class EditProjectGroup
{
    public $client;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    public $description;

    public function __construct(ProjectGroup $projectGroup)
    {
        $this->client = $projectGroup->getClient();
        $this->name = $projectGroup->getName();
        $this->description = $projectGroup->getDescription();
    }
}

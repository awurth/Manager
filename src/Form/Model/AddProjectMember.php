<?php

namespace App\Form\Model;

use App\Entity\Project;
use App\Entity\ProjectMember;
use Symfony\Component\Validator\Constraints as Assert;

class AddProjectMember
{
    public const VALID_ACCESS_LEVELS = [
        ProjectMember::ACCESS_LEVEL_GUEST,
        ProjectMember::ACCESS_LEVEL_MEMBER
    ];

    /**
     * @Assert\NotBlank()
     */
    public $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices=AddProjectMember::VALID_ACCESS_LEVELS)
     */
    public $accessLevel;

    private $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }
}

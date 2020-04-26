<?php

namespace App\Form\Model;

use App\Entity\ProjectGroup;
use App\Entity\ProjectGroupMember;
use Symfony\Component\Validator\Constraints as Assert;

class AddProjectGroupMember
{
    public const VALID_ACCESS_LEVELS = [
        ProjectGroupMember::ACCESS_LEVEL_GUEST,
        ProjectGroupMember::ACCESS_LEVEL_MEMBER
    ];

    /**
     * @Assert\NotBlank()
     */
    public $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices=AddProjectGroupMember::VALID_ACCESS_LEVELS)
     */
    public $accessLevel;

    private $projectGroup;

    public function __construct(ProjectGroup $projectGroup)
    {
        $this->projectGroup = $projectGroup;
    }

    public function getProjectGroup(): ProjectGroup
    {
        return $this->projectGroup;
    }

    public function setProjectGroup(ProjectGroup $projectGroup): void
    {
        $this->projectGroup = $projectGroup;
    }
}

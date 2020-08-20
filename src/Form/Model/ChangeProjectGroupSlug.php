<?php

namespace App\Form\Model;

use App\Entity\ProjectGroup;
use App\Validator\UniqueProjectGroupSlug;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueProjectGroupSlug()
 */
final class ChangeProjectGroupSlug
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[0-9a-z-]+$/")
     */
    public $slug;

    public function __construct(ProjectGroup $projectGroup)
    {
        $this->slug = $projectGroup->getSlug();
    }
}

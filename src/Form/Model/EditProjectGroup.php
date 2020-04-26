<?php

namespace App\Form\Model;

use App\Entity\ProjectGroup;
use Symfony\Component\Validator\Constraints as Assert;

class EditProjectGroup
{
    public $customer;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    public $description;

    public function __construct(ProjectGroup $projectGroup)
    {
        $this->customer = $projectGroup->getCustomer();
        $this->name = $projectGroup->getName();
        $this->description = $projectGroup->getDescription();
    }
}

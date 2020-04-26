<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProjectGroup
{
    public $customer;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^[0-9a-z-]+$/")
     */
    public $slug;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    public $description;
}

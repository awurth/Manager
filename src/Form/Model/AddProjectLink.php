<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class AddProjectLink
{
    public $linkType;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Url()
     */
    public $uri;
}

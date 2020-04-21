<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProjectEnvironment
{
    /**
     * @Assert\NotBlank()
     */
    public $server;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $path;

    /**
     * @Assert\Url()
     * @Assert\Length(max=255)
     */
    public $url;

    public $description;
}

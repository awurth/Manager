<?php

namespace App\Form\Model;

use App\Entity\ProjectEnvironment;
use Symfony\Component\Validator\Constraints as Assert;

class EditProjectEnvironment
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

    public function __construct(ProjectEnvironment  $environment)
    {
        $this->server = $environment->getServer();
        $this->name = $environment->getName();
        $this->path = $environment->getPath();
        $this->url = $environment->getUrl();
        $this->description = $environment->getDescription();
    }
}

<?php

namespace App\Form\Model;

use App\Entity\Link;
use Symfony\Component\Validator\Constraints as Assert;

final class EditProjectLink
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

    public function __construct(Link $link)
    {
        $this->linkType = $link->getLinkType();
        $this->name = $link->getName();
        $this->uri = $link->getUri();
    }
}

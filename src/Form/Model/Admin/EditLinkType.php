<?php

namespace App\Form\Model\Admin;

use App\Entity\LinkType;
use Symfony\Component\Validator\Constraints as Assert;

final class EditLinkType
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Length(max=255)
     */
    public $color;

    /**
     * @Assert\Length(max=255)
     */
    public $uriPrefix;

    /**
     * @Assert\Image()
     */
    public $iconFile;

    public function __construct(LinkType $linkType)
    {
        $this->name = $linkType->getName();
        $this->color = $linkType->getColor();
        $this->uriPrefix = $linkType->getUriPrefix();
    }
}

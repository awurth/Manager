<?php

namespace App\Form\Model\Admin;

use Symfony\Component\Validator\Constraints as Assert;

class CreateLinkType
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
}

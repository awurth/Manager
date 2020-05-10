<?php

namespace App\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class CreateServer
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * @Assert\Ip()
     * @Assert\Length(max=255)
     */
    public $ip;

    /**
     * @Assert\Length(max=255)
     */
    public $operatingSystem;

    /**
     * @Assert\Type("int")
     * @Assert\Range(min=1, max=65536)
     */
    public $sshPort;
}

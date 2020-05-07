<?php

namespace App\Form\Model;

use App\Entity\Server;
use Symfony\Component\Validator\Constraints as Assert;

class EditServer
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

    public function __construct(Server $server)
    {
        $this->name = $server->getName();
        $this->ip = $server->getIp();
        $this->operatingSystem = $server->getOperatingSystem();
    }
}

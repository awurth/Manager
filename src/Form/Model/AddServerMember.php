<?php

namespace App\Form\Model;

use App\Entity\Server;
use App\Entity\ServerMember;
use Symfony\Component\Validator\Constraints as Assert;

final class AddServerMember
{
    public const VALID_ACCESS_LEVELS = [
        ServerMember::ACCESS_LEVEL_GUEST,
        ServerMember::ACCESS_LEVEL_MEMBER
    ];

    /**
     * @Assert\NotBlank()
     */
    public $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices=AddServerMember::VALID_ACCESS_LEVELS)
     */
    public $accessLevel;

    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function setServer(Server $server): void
    {
        $this->server = $server;
    }
}

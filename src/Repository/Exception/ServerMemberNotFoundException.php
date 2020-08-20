<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class ServerMemberNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Server member not found with id "%s"', $id));
    }
}

<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class ServerNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Server not found with id "%s"', (string)$id));
    }
}

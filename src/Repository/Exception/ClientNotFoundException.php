<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class ClientNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Client not found with id "%s"', $id));
    }
}

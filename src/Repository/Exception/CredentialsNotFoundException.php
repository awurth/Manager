<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class CredentialsNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Credentials not found with id "%s"', (string)$id));
    }
}

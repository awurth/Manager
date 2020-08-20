<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class LinkNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Link not found with id "%s"', $id));
    }
}

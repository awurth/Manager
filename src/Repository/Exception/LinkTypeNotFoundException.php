<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class LinkTypeNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Link type not found with id "%s"', $id));
    }
}

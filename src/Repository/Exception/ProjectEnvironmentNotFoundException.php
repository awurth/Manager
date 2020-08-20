<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class ProjectEnvironmentNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Project environment not found with id "%s"', $id));
    }
}

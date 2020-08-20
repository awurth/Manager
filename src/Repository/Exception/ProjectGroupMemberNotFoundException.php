<?php

namespace App\Repository\Exception;

use App\Entity\ValueObject\Id;

final class ProjectGroupMemberNotFoundException extends EntityNotFoundException
{
    public static function byId(Id $id): self
    {
        return new self(sprintf('Project group member not found with id "%s"', $id));
    }
}

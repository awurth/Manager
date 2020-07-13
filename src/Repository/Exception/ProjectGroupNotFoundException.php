<?php

namespace App\Repository\Exception;

class ProjectGroupNotFoundException extends EntityNotFoundException
{
    public static function bySlug(string $slug): self
    {
        return new self(sprintf('Project group not found with slug "%s"', $slug));
    }
}

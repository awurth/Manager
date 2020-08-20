<?php

namespace App\Repository\Exception;

final class ProjectNotFoundException extends EntityNotFoundException
{
    public static function bySlug(string $slug): self
    {
        return new self(sprintf('Project not found with slug "%s"', $slug));
    }
}

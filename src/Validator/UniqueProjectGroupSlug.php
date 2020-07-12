<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueProjectGroupSlug extends Constraint
{
    public string $message = 'The slug "{{ slug }}" is already used.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

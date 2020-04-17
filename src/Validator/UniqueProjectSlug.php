<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueProjectSlug extends Constraint
{
    public $message = 'The slug "{{ slug }}" is already used.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

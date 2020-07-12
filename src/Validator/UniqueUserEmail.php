<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueUserEmail extends Constraint
{
    public string $message = 'The email "{{ email }}" is already used.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}

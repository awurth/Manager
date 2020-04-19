<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueUserEmailValidator extends ConstraintValidator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($model, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUserEmail) {
            throw new UnexpectedTypeException($constraint, UniqueUserEmail::class);
        }

        $existingUser = $this->userRepository->findOneBy(['email' => $model->email]);

        if ($existingUser) {
            $this->context->buildViolation($constraint->message)
                ->atPath('email')
                ->setParameter('{{ email }}', $model->email)
                ->setInvalidValue($model->email)
                ->addViolation();
        }
    }
}

<?php

namespace App\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueUserEmailValidator extends ConstraintValidator
{
    private UserRepository $userRepository;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUserEmail) {
            throw new UnexpectedTypeException($constraint, UniqueUserEmail::class);
        }

        $user = $this->propertyAccessor->isReadable($value, 'user')
            ? $this->propertyAccessor->getValue($value, 'user')
            : null;

        $existingUser = $this->getExistingUser($value->email, $user);

        if ($existingUser) {
            $this->context->buildViolation($constraint->message)
                ->atPath('email')
                ->setParameter('{{ email }}', $value->email)
                ->setInvalidValue($value->email)
                ->addViolation();
        }
    }

    private function getExistingUser(string $email, ?User $user = null): ?User
    {
        $qb = $this->userRepository->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        if ($user) {
            $qb
                ->andWhere('u != :user')
                ->setParameter('user', $user);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}

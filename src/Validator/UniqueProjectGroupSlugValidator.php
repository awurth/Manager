<?php

namespace App\Validator;

use App\Repository\ProjectGroupRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueProjectGroupSlugValidator extends ConstraintValidator
{
    private ProjectGroupRepository $projectGroupRepository;

    public function __construct(ProjectGroupRepository $projectGroupRepository)
    {
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueProjectGroupSlug) {
            throw new UnexpectedTypeException($constraint, UniqueProjectGroupSlug::class);
        }

        $existingProject = $this->projectGroupRepository->findOneBy(['slug' => $value->slug]);

        if ($existingProject) {
            $this->context->buildViolation($constraint->message)
                ->atPath('slug')
                ->setParameter('{{ slug }}', $value->slug)
                ->setInvalidValue($value->slug)
                ->addViolation();
        }
    }
}

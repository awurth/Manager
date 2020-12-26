<?php

namespace App\Validator;

use App\Repository\ProjectRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueProjectSlugValidator extends ConstraintValidator
{
    private ProjectRepository $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueProjectSlug) {
            throw new UnexpectedTypeException($constraint, UniqueProjectSlug::class);
        }

        $existingProject = $this->projectRepository->findOneBy(['slug' => $value->slug]);

        if ($existingProject) {
            $this->context->buildViolation($constraint->message)
                ->atPath('slug')
                ->setParameter('{{ slug }}', $value->slug)
                ->setInvalidValue($value->slug)
                ->addViolation();
        }
    }
}

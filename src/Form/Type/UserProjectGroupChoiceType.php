<?php

namespace App\Form\Type;

use App\Repository\ProjectGroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class UserProjectGroupChoiceType extends AbstractType
{
    private $projectGroupRepository;
    private $security;

    public function __construct(ProjectGroupRepository $projectGroupRepository, Security $security)
    {
        $this->projectGroupRepository = $projectGroupRepository;
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_label' => 'name',
            'choice_value' => 'id',
            'choice_translation_domain' => false
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getChoices(): array
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->projectGroupRepository->findBy([], ['updatedAt' => 'DESC', 'createdAt' => 'DESC']);
        }

        return $this->projectGroupRepository->createQueryBuilder('g')
            ->join('g.members', 'm')
            ->where('m.user = :user')
            ->setParameter('user', $user = $this->security->getUser())
            ->orderBy('g.updatedAt', 'DESC')
            ->addOrderBy('g.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}

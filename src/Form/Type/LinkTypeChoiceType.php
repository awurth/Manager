<?php

namespace App\Form\Type;

use App\Repository\LinkTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkTypeChoiceType extends AbstractType
{
    private $linkTypeRepository;

    public function __construct(LinkTypeRepository $linkTypeRepository)
    {
        $this->linkTypeRepository = $linkTypeRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_label' => 'name',
            'choice_value' => 'id'
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getChoices(): array
    {
        return $this->linkTypeRepository->findBy([], ['name' => 'ASC']);
    }
}

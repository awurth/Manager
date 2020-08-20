<?php

namespace App\Form\Type;

use App\Entity\LinkType;
use App\Repository\LinkTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkTypeChoiceType extends AbstractType
{
    private LinkTypeRepository $linkTypeRepository;

    public function __construct(LinkTypeRepository $linkTypeRepository)
    {
        $this->linkTypeRepository = $linkTypeRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getChoices(),
            'choice_attr' => static function (LinkType $linkType) {
                return ['data-uri-prefix' => $linkType->getUriPrefix()];
            },
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
        return $this->linkTypeRepository->findBy([], ['name' => 'ASC']);
    }
}

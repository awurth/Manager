<?php

namespace App\Form\Type\Action;

use App\Form\Model\EditProjectLink;
use App\Form\Type\LinkTypeChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('linkType', LinkTypeChoiceType::class, [
                'required' => false
            ])
            ->add('name')
            ->add('uri', UrlType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditProjectLink::class
        ]);
    }
}

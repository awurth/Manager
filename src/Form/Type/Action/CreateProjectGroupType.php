<?php

namespace App\Form\Type\Action;

use App\Form\Model\CreateProjectGroup;
use App\Form\Type\ClientChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateProjectGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('slug')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('client', ClientChoiceType::class, [
                'placeholder' => 'select_client',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateProjectGroup::class
        ]);
    }
}

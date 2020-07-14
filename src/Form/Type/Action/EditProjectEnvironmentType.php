<?php

namespace App\Form\Type\Action;

use App\Form\Model\EditProjectEnvironment;
use App\Form\Type\ServerChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectEnvironmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('server', ServerChoiceType::class)
            ->add('name')
            ->add('path')
            ->add('url', UrlType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditProjectEnvironment::class
        ]);
    }
}

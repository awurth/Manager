<?php

namespace App\Form\Type\Action;

use App\Form\Model\CreateProject;
use App\Form\Type\UserProjectGroupChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('projectGroup', UserProjectGroupChoiceType::class)
            ->add('name')
            ->add('slug')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('logoFile', FileType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateProject::class
        ]);
    }
}

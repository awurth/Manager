<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\ProjectType;
use App\Form\Model\CreateProject;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CreateProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('slug')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false
            ])
            ->add('type', EntityType::class, [
                'class' => ProjectType::class,
                'placeholder' => 'select_project_type',
                'required' => false
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'placeholder' => 'select_customer',
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

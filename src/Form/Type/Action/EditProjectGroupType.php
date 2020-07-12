<?php

namespace App\Form\Type\Action;

use App\Entity\Client;
use App\Form\Model\EditProjectGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProjectGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_value' => 'id',
                'placeholder' => 'select_client',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditProjectGroup::class
        ]);
    }
}

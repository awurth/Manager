<?php

namespace App\Form\Type\Action;

use App\Form\Model\GeneratePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GeneratePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uppercase', CheckboxType::class, [
                'required' => false
            ])
            ->add('lowercase', CheckboxType::class, [
                'required' => false
            ])
            ->add('numbers', CheckboxType::class, [
                'required' => false
            ])
            ->add('symbols', CheckboxType::class, [
                'required' => false
            ])
            ->add('length', IntegerType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneratePassword::class
        ]);
    }
}

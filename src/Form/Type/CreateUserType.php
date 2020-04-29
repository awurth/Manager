<?php

namespace App\Form\Type;

use App\Form\Model\CreateUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'user.role.user' => 'ROLE_USER',
                    'user.role.admin' => 'ROLE_ADMIN'
                ],
                'expanded' => true
            ])
            ->add('gender', GenderType::class, [
                'expanded' => true
            ])
            ->add('firstname')
            ->add('lastname');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateUser::class
        ]);
    }
}

<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenderType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'gender_neutral' => User::GENDER_NEUTRAL,
                'gender_female' => User::GENDER_FEMALE,
                'gender_male' => User::GENDER_MALE
            ]
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}

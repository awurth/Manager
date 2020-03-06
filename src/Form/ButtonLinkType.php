<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonLinkType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'translation_domain' => 'admin_messages'
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'button_link';
    }
}

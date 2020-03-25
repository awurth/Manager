<?php

namespace App\Form\Admin;

use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HtmlTextType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'disabled' => true,
            'required' => false
        ]);
    }

    public function transform($value)
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('d/m/Y - H\hi');
        }

        return $value;
    }

    public function reverseTransform($value)
    {
        return $value;
    }
}

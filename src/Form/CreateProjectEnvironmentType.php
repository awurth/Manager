<?php

namespace App\Form;

use App\Entity\Server;
use App\Form\Model\CreateProjectEnvironment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProjectEnvironmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('server', EntityType::class, [
                'class' => Server::class
            ])
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
            'data_class' => CreateProjectEnvironment::class
        ]);
    }
}

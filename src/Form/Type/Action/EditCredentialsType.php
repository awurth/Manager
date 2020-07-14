<?php

namespace App\Form\Type\Action;

use App\Entity\User;
use App\Form\Model\EditCredentials;
use App\Repository\UserRepository;
use App\Security\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditCredentialsType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('username')
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('password')
            ->add('website', UrlType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_value' => 'id',
                'multiple' => true,
                'query_builder' => function (UserRepository $repository) {
                    return $repository->createQueryBuilder('u')
                        ->where('u != :user')
                        ->setParameter('user', $this->security->getUser()->getId(), 'uuid_binary');
                },
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditCredentials::class
        ]);
    }
}

<?php

namespace App\Form\Type\Action;

use App\Form\Model\CreateCredentials;
use App\Repository\UserRepository;
use App\Security\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CreateCredentialsType extends AbstractType
{
    private Security $security;
    private UserRepository $userRepository;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
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
            ->add('users', ChoiceType::class, [
                'choices' => $this->getUsersChoices(),
                'choice_label' => 'email',
                'choice_value' => 'id',
                'choice_translation_domain' => false,
                'multiple' => true,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CreateCredentials::class
        ]);
    }

    private function getUsersChoices(): array
    {
        return $this->userRepository->createQueryBuilder('u')
            ->where('u != :user')
            ->setParameter('user', $this->security->getUser()->getId(), 'uuid_binary')
            ->getQuery()->getResult();
    }
}

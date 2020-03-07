<?php

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'user';

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @param User $user
     */
    public function prePersist($user): void
    {
        $user
            ->addRole('ROLE_ADMIN')
            ->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
    }

    /**
     * @param User $user
     */
    public function preUpdate($user)
    {
        if ($user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        }
    }

    public function setPasswordEncoder(UserPasswordEncoderInterface $passwordEncoder): void
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('email')
            ->add('plainPassword', TextType::class, [
                'required' => !$this->getSubject() || !$this->getSubject()->getId()
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'gender_female' => User::GENDER_FEMALE,
                    'gender_male' => User::GENDER_MALE,
                    'gender_neutral' => User::GENDER_NEUTRAL
                ]
            ])
            ->add('firstname')
            ->add('lastname');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('gender', 'choice', [
                'choices' => array_map([$this, 'trans'], [
                    User::GENDER_FEMALE => 'gender_female',
                    User::GENDER_MALE => 'gender_male',
                    User::GENDER_NEUTRAL => 'gender_neutral'
                ]),
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('firstname', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('lastname', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('email', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ]);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection
            ->remove('batch')
            ->remove('export')
            ->remove('show');
    }
}

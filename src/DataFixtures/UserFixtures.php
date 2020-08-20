<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Form\Model\Admin\CreateUser;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private UserRepository $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        if ($this->userRepository->findOneBy(['email' => 'contact@alexiswurth.fr'])) {
            return;
        }

        $createUser = new CreateUser();
        $createUser->email = 'contact@alexiswurth.fr';
        $createUser->firstname = 'Alexis';
        $createUser->lastname = 'Wurth';
        $createUser->role = 'ROLE_ADMIN';
        $createUser->plainPassword = 'admin';

        $user = User::createFromAdminCreationForm($createUser, $this->passwordEncoder);

        $manager->persist($user);
        $manager->flush();
    }
}

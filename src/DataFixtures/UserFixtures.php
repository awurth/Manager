<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Form\Model\Admin\CreateUser;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->passwordHasher = $passwordHasher;
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

        $user = User::createFromAdminCreationForm($createUser, $this->passwordHasher);

        $manager->persist($user);
        $manager->flush();
    }
}

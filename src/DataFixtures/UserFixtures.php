<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Form\Model\Admin\CreateUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        if ($manager->getRepository(User::class)->findOneBy(['email' => 'contact@alexiswurth.fr'])) {
            return;
        }

        $createUser = new CreateUser();
        $createUser->email = 'contact@alexiswurth.fr';
        $createUser->gender = User::GENDER_NEUTRAL;
        $createUser->firstname = 'Alexis';
        $createUser->lastname = 'Wurth';
        $createUser->role = 'ROLE_ADMIN';
        $createUser->plainPassword = 'admin';

        $user = User::createFromAdminCreationForm($createUser, $this->passwordEncoder);

        $manager->persist($user);
        $manager->flush();
    }
}

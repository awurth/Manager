<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        if ($manager->getRepository(User::class)->findOneBy(['email' => 'contact@alexiswurth.fr'])) {
            return;
        }

        $user = (new User('contact@alexiswurth.fr', 'Alexis', 'Wurth'))
            ->setRoles(['ROLE_ADMIN']);

        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));

        $manager->persist($user);
        $manager->flush();
    }
}

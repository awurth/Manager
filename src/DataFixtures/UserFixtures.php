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
        if ($manager->getRepository(User::class)->findOneBy(['email' => 'admin@pilipili-web.com'])) {
            return;
        }

        $user = (new User())
            ->setEmail('admin@pilipili-web.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setGender(User::GENDER_MALE);

        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));

        $manager->persist($user);
        $manager->flush();
    }
}

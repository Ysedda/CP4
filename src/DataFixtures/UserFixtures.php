<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setAddress($faker->address());
            $user->setEmail('user' . $i . '@user.com');
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setPassword('user');
            $user->setIsVerified(true);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'user'
            );
            $user->setPassword($hashedPassword);
            $user->setPhone($faker->phoneNumber());
            $this->addReference('user' . $i, $user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}

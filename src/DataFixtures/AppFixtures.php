<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Card;
use App\Entity\User;
use App\Entity\Set;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
        /**
     * @var Generator
     */
    private Generator $faker;

    /**
     * Password Hasher
     *
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher){
        $this->faker = Factory::create('fr_FR');
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $password = 'Doe';
        $user->setUsername('John');
        $user->setRoles(["USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $manager->persist($user);

        $set = (new Set())
            ->setName('Set 1')
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setStatus('active');
        $manager->persist($set);

        for ($i = 0; $i < 10; $i++) {
            $card = (new Card())
                ->setName('Card ' . $i)
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setSet($set)
                ->setStatus('active');
            $manager->persist($card);
        }

        $manager->flush();
    }
}

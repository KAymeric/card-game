<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Card;
use App\Entity\User;
use App\Entity\Set;
use App\Entity\Type;
use App\Entity\Stat;
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
        $this->faker = Factory::create('fr_FR');

        $user = new User();
        $password = 'Doe';
        $user->setUsername('John');
        $user->setRoles(["USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $manager->persist($user);

        for ($i = 0; $i < 5; $i++) {
            $set = (new Set())
                ->setName('Set '. $i)
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setStatus('active');
            $manager->persist($set);

            $type = (new Type())
                ->setName($this->faker->realText(10))
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setStatus('active');
            $manager->persist($type);

            $stat1 = (new Stat())
                ->setName($this->faker->realText(10))
                ->setValue($this->faker->numberBetween(1, 10))
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setStatus('active');
            $manager->persist($stat1);

            $stat2 = (new Stat())
                ->setName($this->faker->realText(10))
                ->setValue($this->faker->numberBetween(1, 10))
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setStatus('active');
            $manager->persist($stat2);

            for ($j = 0; $j < 10; $j++) {
                $card = (new Card())
                    ->setName($this->faker->realText(10))
                    ->setDescription($this->faker->realText(100))
                    ->setUpdatedAt(new \DateTimeImmutable())
                    ->setSet($set)
                    ->setType($type)
                    ->addStat($stat1)
                    ->addStat($stat2)
                    ->setStatus('active');
                $manager->persist($card);
            }
        }
        
        $manager->flush();
    }
}

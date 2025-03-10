<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Card;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $card = (new Card())
                ->setName('Card ' . $i)
                ->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($card);
        }

        $admin = (new User())
            ->setUsername('admin')
            ->setPassword(('admin'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}

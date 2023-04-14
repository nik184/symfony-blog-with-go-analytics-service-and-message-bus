<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $author = new User();
        $author->setRoles([User::ROLE_AUTHOR]);
        $author->setUsername('author');
        $author->setPassword($this->hasher->hashPassword($author, 'author'));

        $moderator = new User();
        $moderator->setRoles([User::ROLE_MODERATOR]);
        $moderator->setUsername('moderator');
        $moderator->setPassword($this->hasher->hashPassword($moderator, 'moderator'));

        $manager->persist($author);
        $manager->persist($moderator);

        $manager->flush();
    }
}

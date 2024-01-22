<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // Création d'un utilisateur normal
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->hashPassword(
            $user,
            'secrurepassword'// Mot de passe en clair à encoder
        ));
        $user->setUsername('user');
        $manager->persist($user);

        // Création d'un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->hashPassword(
            $admin,
            'securepassword' // Mot de passe en clair à encoder
        ));
        $admin->setUsername('admin');
        $manager->persist($admin);

        // Enregistrement des utilisateurs dans la base de données
        $manager->flush();
    }
}
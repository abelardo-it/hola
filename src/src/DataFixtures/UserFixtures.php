<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadAdminUser($manager);
        $this->loadUser1($manager);
        $this->loadUser2($manager);
    }

    private function loadAdminUser(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('Admin');
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'adminpassword'
        ));
        $user->setRoles(['ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }

    private function loadUser1(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('User1');
        $user->setUsername('page1');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'page1'
        ));
        $user->setRoles(['PAGE_1']);
        $manager->persist($user);
        $manager->flush();
    }

    private function loadUser2(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('User2');
        $user->setUsername('page2');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'page2'
        ));
        $user->setRoles(['PAGE_2']);
        $manager->persist($user);
        $manager->flush();
    }
}

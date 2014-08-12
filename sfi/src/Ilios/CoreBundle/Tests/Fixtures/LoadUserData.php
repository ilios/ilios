<?php

namespace Ilios\CoreBundle\Tests\Fixtures;

use Ilios\CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName('first');
        $user->setLastName('last');
        $user->setEmail('first@example.com');
        $user->setUcUid('123456789');
        $user->setPrimarySchool($this->getReference('school1'));

        $manager->persist($user);


        $user = new User();
        $user->setFirstName('first');
        $user->setLastName('last');
        $user->setEmail('second@example.com');
        $user->setUcUid('123456798');
        $user->setPrimarySchool($this->getReference('school1'));

        $manager->persist($user);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixtures\LoadSchoolData'
        );
    }
}

<?php

namespace Ilios\CoreBundle\Tests\Fixtures;

use Ilios\CoreBundle\Entity\School;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSchoolData extends AbstractFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $school = new School();
        $school->setTitle('First School');
        $school->setIliosAdministratorEmail('root@example.com');
        $manager->persist($school);

        $school2 = new School();
        $school2->setTitle('Second School');
        $school2->setIliosAdministratorEmail('root@example.com');

        $manager->persist($school2);
        $manager->flush();
        $this->addReference('school1', $school);
        $this->addReference('school2', $school2);
    }
}

<?php

namespace Ilios\CoreBundle\Tests\Fixtures;

use Ilios\CoreBundle\Entity\Offering;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadOfferingData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $offering = new Offering();
        $offering->setStartDate(new \DateTime('midnight yesterday'));
        $offering->setEndDate(new \DateTime('midnight today'));
        $offering->setRoom('Test Room');
        $offering->setLastUpdatedOn(new \DateTime());
        $offering->setSession($this->getReference('session1'));
        $manager->persist($offering);


        $manager->persist($offering);
        $manager->flush();
        $this->addReference('offering1', $offering);
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixtures\LoadSessionData'
        );
    }
}

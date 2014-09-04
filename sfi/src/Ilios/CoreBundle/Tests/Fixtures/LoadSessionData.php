<?php

namespace Ilios\CoreBundle\Tests\Fixtures;

use Ilios\CoreBundle\Entity\Session;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadSessionData extends AbstractFixture implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $session = new Session();
        $session->setTitle('First Test Session');
        $session->setLastUpdatedOn(new \DateTime());
        $manager->persist($session);

        $manager->flush();
        $this->addReference('session1', $session);
    }
}
    // private $sessionId;
    // private $title;
    // private $supplemental;
    // private $deleted;
    // private $publishedAsTbd;
    // private $lastUpdatedOn;
    // private $sessionType;
    // private $course;
    // private $ilmSessionFacet;
    // private $disciplines;
    // private $objectives;
    // private $meshDescriptors;
    // private $publishEvent;

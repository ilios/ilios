<?php

namespace Ilios\CoreBundle\Tests\Fixtures;

use Ilios\CoreBundle\Entity\Objective;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadObjectiveData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $objective = new Objective();
        $objective->setTitle('one');

        $manager->persist($objective);


        $objective = new Objective();
        $objective->setTitle('two');

        $manager->persist($objective);
        $manager->flush();
    }
}

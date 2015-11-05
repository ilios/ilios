<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\Program;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProgramData extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('ilioscore.dataloader.program')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Program();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setShortTitle($arr['shortTitle']);
            $entity->setDuration($arr['duration']);
            $entity->setPublishedAsTbd($arr['publishedAsTbd']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            if (!empty($arr['publishEvent'])) {
                $entity->setPublishEvent($this->getReference('publishEvents' . $arr['publishEvent']));
            }
            $manager->persist($entity);
            $this->addReference('programs' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
        );
    }
}

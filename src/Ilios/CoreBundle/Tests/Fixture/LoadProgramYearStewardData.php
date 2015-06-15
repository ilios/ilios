<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\ProgramYearSteward;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProgramYearStewardData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('ilioscore.dataloader.programYearSteward')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new ProgramYearSteward();
            $entity->setId($arr['id']);
            $manager->persist($entity);
            $this->addReference('programYearStewards' . $arr['id'], $entity);
        }

        $manager->flush();
    }
}

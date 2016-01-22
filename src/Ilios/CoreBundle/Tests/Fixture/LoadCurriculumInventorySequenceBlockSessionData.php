<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSession;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventorySequenceBlockSessionData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.curriculumInventorySequenceBlockSession')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventorySequenceBlockSession();
            $entity->setId($arr['id']);
            $entity->setSequenceBlock($this->getReference('curriculumInventorySequenceBlocks' . $arr['sequenceBlock']));
            $entity->setCountOfferingsOnce($arr['countOfferingsOnce']);
            $entity->setSession($this->getReference('sessions' . $arr['session']));
            $manager->persist($entity);
            $this->addReference('curriculumInventorySequenceBlockSessions' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
        );
    }
}

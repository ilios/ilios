<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlock;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventorySequenceBlockData extends AbstractFixture implements
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
            ->get('ilioscore.dataloader.curriculumInventorySequenceBlock')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventorySequenceBlock();
            $entity->setId($arr['id']);
            $manager->persist($entity);
            $this->addReference('curriculumInventorySequenceBlocks' . $arr['id'], $entity);
        }

        $manager->flush();
    }

}

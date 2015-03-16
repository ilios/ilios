<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\AamcMethod;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Users extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $aamcMethods = $this->container->get('ilioscore.dataloader.aamcmethods')
            ->get();
        foreach ($aamcMethods as $arr) {
            $aamcMethod = new AamcMethod();
            $aamcMethod->setId($arr['id']);

            $manager->persist($aamcMethod);
            $this->addReference('aamcMethod' + $arr['id'], $aamcMethod);
        }

        //We have to disable auto id generation in order to save with ID
        $metadata = $manager->getClassMetaData(get_class($aamcMethod));
        $metadata->setIdGeneratorType(
            \Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE
        );
        $manager->flush();

    }
}

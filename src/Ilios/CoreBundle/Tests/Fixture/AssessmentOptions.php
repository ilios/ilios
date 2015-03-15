<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\AssessmentOption;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AssessmentOptions extends AbstractFixture implements
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
        $assessmentOptions = $this->container->get(
          'ilioscore.dataloader.assessmentoptions'
        )->get();
        foreach ($assessmentOptions as $arr) {
            $assessmentOptions = new AssessmentOption();
            $assessmentOptions->setId($arr['id']);
            $manager->persist($assessmentOptions);
            $this->addReference(
              'assessmentOption' + $arr['id'],
              $assessmentOptions
            );
        }

        $metadata = $manager->getClassMetaData(get_class($assessmentOptions));
        $metadata->setIdGeneratorType(
          \Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE
        );
        $manager->flush();

    }
}

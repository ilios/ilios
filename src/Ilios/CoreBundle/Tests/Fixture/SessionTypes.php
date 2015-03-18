<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Ilios\CoreBundle\Entity\SessionType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SessionTypes extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
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
        $sessionTypes = $this->container->get(
            'ilioscore.dataloader.sessiontypes'
        )->get();

        foreach ($sessionTypes as $arr) {
            $sessionType = new SessionType();
            $sessionType->setId($arr['id']);
            $sessionType->setTitle($arr['title']);
            $sessionType->setAssessmentOption(
                $this->getReference(
                    'assessmentOption' + $arr['assessmentOption']
                )
            );
            $sessionType->setOwningSchool(
                $this->getReference('school' + $arr['owningSchool'])
            );

            foreach ($arr['aamcMethods'] as $aamcMethodId) {
                $sessionType->addAamcMethod(
                    $this->getReference('aamcMethod' + $aamcMethodId)
                );
            }

            $manager->persist($sessionType);
            $this->addReference('sessionType' + $arr['id'], $sessionType);
        }

        $metadata = $manager->getClassMetaData(get_class($sessionType));
        $metadata->setIdGeneratorType(
            \Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE
        );
        $manager->flush();

    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\Schools',
            'Ilios\CoreBundle\Tests\Fixture\AssessmentOptions',
            'Ilios\CoreBundle\Tests\Fixture\AamcMethods'
        );
    }
}

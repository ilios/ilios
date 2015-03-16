<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\School;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Schools extends AbstractFixture implements
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
        $schools = $this->container->get('ilioscore.dataloader.schools')->get();
        foreach ($schools as $arr) {
            $school = new School();
            $school->setId($arr['id']);
            $school->setIliosAdministratorEmail(
                $arr['iliosAdministratorEmail']
            );
            $school->setDeleted($arr['deleted']);
            $school->setChangeAlertRecipients($arr['changeAlertRecipients']);

            $manager->persist($school);
            $this->addReference('school' + $arr['id'], $school);
        }

        //We have to disable auto id generation in order to save with ID
        $metadata = $manager->getClassMetaData(get_class($school));
        $metadata->setIdGeneratorType(
            \Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE
        );
        $manager->flush();

    }
}

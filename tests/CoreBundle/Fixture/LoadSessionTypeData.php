<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\SessionType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionTypeData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\SessionTypeData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new SessionType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setCalendarColor($arr['calendarColor']);
            $entity->setAssessment($arr['assessment']);
            $entity->setActive($arr['active']);
            $entity->setAssessmentOption(
                $this->getReference('assessmentOptions' . $arr['assessmentOption'])
            );
            $entity->setSchool($this->getReference('schools' . $arr['school']));

            foreach ($arr['aamcMethods'] as $id) {
                $entity->addAamcMethod($this->getReference('aamcMethods' . $id));
            }
            $manager->persist($entity);
            $this->addReference('sessionTypes' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadAssessmentOptionData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
        );
    }
}

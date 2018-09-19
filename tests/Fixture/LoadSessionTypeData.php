<?php

namespace Tests\App\Fixture;

use App\Entity\SessionType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionTypeData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('Tests\AppBundle\DataLoader\SessionTypeData')
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
            'Tests\AppBundle\Fixture\LoadAamcMethodData',
            'Tests\AppBundle\Fixture\LoadAssessmentOptionData',
            'Tests\AppBundle\Fixture\LoadSchoolData',
        );
    }
}

<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\Course;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface,
    DependentFixtureInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('ilioscore.dataloader.course')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Course();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setLevel($arr['level']);
            $entity->setYear($arr['year']);
            $entity->setStartDate(new \DateTime($arr['startDate']));
            $entity->setEndDate(new \DateTime($arr['endDate']));
            $entity->setExternalId($arr['externalId']);
            $entity->setLocked($arr['locked']);
            $entity->setArchived($arr['archived']);
            $entity->setPublishedAsTbd($arr['publishedAsTbd']);
            $entity->setPublished($arr['published']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            if (isset($arr['clerkshipType'])) {
                $entity->setClerkshipType($this->getReference('courseClerkshipTypes' . $arr['clerkshipType']));
            }
            foreach ($arr['cohorts'] as $id) {
                $entity->addCohort($this->getReference('cohorts' . $id));
            }
            foreach ($arr['directors'] as $id) {
                $entity->addDirector($this->getReference('users' . $id));
            }
            foreach ($arr['topics'] as $id) {
                $entity->addTopic($this->getReference('topics' . $id));
            }
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id));
            }
            foreach ($arr['objectives'] as $id) {
                $entity->addObjective($this->getReference('objectives' . $id));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $manager->persist($entity);
            
            $this->addReference('courses' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadTermData',
            'Ilios\CoreBundle\Tests\Fixture\LoadTopicData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseClerkshipTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
        );
    }
}

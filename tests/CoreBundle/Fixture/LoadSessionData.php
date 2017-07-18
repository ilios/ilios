<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\Session;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadSessionData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\SessionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new Session();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);

            $related = array(
                'attireRequired' => 'setAttireRequired',
                'equipmentRequired' => 'setEquipmentRequired',
                'supplemental' => 'setSupplemental',
                'attendanceRequired' => 'setAttendanceRequired',
            );
            foreach ($related as $key => $method) {
                if (array_key_exists($key, $arr)) {
                    $entity->$method($arr[$key]);
                }
            }
            $entity->setPublishedAsTbd($arr['publishedAsTbd']);
            $entity->setPublished($arr['published']);
            if (!empty($arr['sessionType'])) {
                $entity->setSessionType($this->getReference('sessionTypes' . $arr['sessionType']));
            }
            if (!empty($arr['course'])) {
                $entity->setCourse($this->getReference('courses' . $arr['course']));
            }
            $related = array(
                'terms' => 'addTerm',
                'objectives' => 'addObjective',
                'meshDescriptors' => 'addMeshDescriptor',
            );
            foreach ($related as $key => $method) {
                foreach ($arr[$key] as $id) {
                    $entity->$method($this->getReference($key . $id));
                }
            }
            foreach ($arr['administrators'] as $id) {
                $entity->addAdministrator($this->getReference('users' . $id));
            }
            $manager->persist($entity);

            $this->addReference('sessions' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadUserData',
        );
    }
}

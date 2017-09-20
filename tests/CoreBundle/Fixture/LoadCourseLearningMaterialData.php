<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\CourseLearningMaterial;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseLearningMaterialData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\CourseLearningMaterialData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CourseLearningMaterial();
            $entity->setId($arr['id']);
            $entity->setRequired($arr['required']);
            $entity->setPublicNotes($arr['publicNotes']);
            $entity->setNotes($arr['notes']);
            $entity->setPosition($arr['position']);
            if (!is_null($arr['startDate'])) {
                $entity->setStartDate(new \DateTime($arr['startDate']));
            }
            if (!is_null($arr['endDate'])) {
                $entity->setEndDate(new \DateTime($arr['endDate']));
            }
            $entity->setCourse($this->getReference('courses' . $arr['course']));
            $entity->setLearningMaterial($this->getReference('learningMaterials' . $arr['learningMaterial']));
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $manager->persist($entity);
            $this->addReference('courseLearningMaterials' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
        );
    }
}

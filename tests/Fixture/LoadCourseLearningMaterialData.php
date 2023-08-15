<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CourseLearningMaterial;
use App\Repository\RepositoryInterface;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCourseLearningMaterialData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\CourseLearningMaterialData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(CourseLearningMaterial::class);
        foreach ($data as $arr) {
            $entity = new CourseLearningMaterial();
            $entity->setId($arr['id']);
            $entity->setRequired($arr['required']);
            $entity->setPublicNotes($arr['publicNotes']);
            $entity->setNotes($arr['notes']);
            $entity->setPosition($arr['position']);
            if (!is_null($arr['startDate'])) {
                $entity->setStartDate(new DateTime($arr['startDate']));
            }
            if (!is_null($arr['endDate'])) {
                $entity->setEndDate(new DateTime($arr['endDate']));
            }
            $entity->setCourse($this->getReference('courses' . $arr['course']));
            $entity->setLearningMaterial($this->getReference('learningMaterials' . $arr['learningMaterial']));
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id));
            }
            $repository->update($entity, false, true);
            $this->addReference('courseLearningMaterials' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
        ];
    }
}

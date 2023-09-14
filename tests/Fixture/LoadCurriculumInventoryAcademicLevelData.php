<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CurriculumInventoryAcademicLevel;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryAcademicLevelData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\CurriculumInventoryAcademicLevelData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(CurriculumInventoryAcademicLevel::class);
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryAcademicLevel();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setLevel($arr['level']);
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $entity->setDescription($arr['description']);
            $repository->update($entity, true, true);
            $this->addReference('curriculumInventoryAcademicLevels' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadCurriculumInventoryReportData',
        ];
    }
}

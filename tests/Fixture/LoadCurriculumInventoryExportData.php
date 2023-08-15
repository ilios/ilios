<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CurriculumInventoryExport;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryExportData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\CurriculumInventoryExportData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(CurriculumInventoryExport::class);
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryExport();
            $entity->setId($arr['id']);
            $entity->setReport($this->getReference('curriculumInventoryReports' . $arr['report']));
            $entity->setCreatedBy($this->getReference('users' . $arr['createdBy']));
            $entity->setDocument($arr['document']);
            $repository->update($entity, true, true);
            $this->addReference('curriculumInventoryExports' . $arr['report'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadCurriculumInventoryReportData',
        ];
    }
}

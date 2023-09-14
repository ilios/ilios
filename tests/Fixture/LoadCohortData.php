<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Cohort;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCohortData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\CohortData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(Cohort::class);
        foreach ($data as $arr) {
            $entity = new Cohort();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setProgramYear($this->getReference('programYears' . $arr['programYear']));
            $repository->update($entity, true, true);
            $this->addReference('cohorts' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadProgramYearData'
        ];
    }
}

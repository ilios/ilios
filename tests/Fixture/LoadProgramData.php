<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Program;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProgramData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\ProgramData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(Program::class);
        foreach ($data as $arr) {
            $entity = new Program();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setShortTitle($arr['shortTitle']);
            $entity->setDuration($arr['duration']);
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            $repository->update($entity, true, true);
            $this->addReference('programs' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
    {
        return [
            'App\Tests\Fixture\LoadSchoolData',
        ];
    }
}

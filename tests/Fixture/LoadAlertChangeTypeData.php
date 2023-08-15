<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AlertChangeType;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAlertChangeTypeData extends AbstractFixture implements
    ORMFixtureInterface,
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
            ->get('App\Tests\DataLoader\AlertChangeTypeData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(AlertChangeType::class);
        foreach ($data as $arr) {
            $entity = new AlertChangeType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);

            $repository->update($entity, true, true);
            $this->addReference('alertChangeTypes' . $arr['id'], $entity);
        }
        $repository->flush();
    }
}

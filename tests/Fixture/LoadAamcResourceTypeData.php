<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\AamcResourceType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadAamcResourceTypeData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\AamcResourceTypeData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(AamcResourceType::class);
        foreach ($data as $arr) {
            $entity = new AamcResourceType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $repository->update($entity, false, true);
            $this->addReference('aamcResourceTypes' . $arr['id'], $entity);
        }
        $repository->flush();
    }
}

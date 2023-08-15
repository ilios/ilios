<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearningMaterialStatus;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadLearningMaterialStatusData extends AbstractFixture implements
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
            ->get('App\Tests\DataLoader\LearningMaterialStatusData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(LearningMaterialStatus::class);
        foreach ($data as $arr) {
            $entity = new LearningMaterialStatus();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $repository->update($entity, false, true);
            $this->addReference('learningMaterialStatus' . $arr['id'], $entity);
        }
        $repository->flush();
    }
}

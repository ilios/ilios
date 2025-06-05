<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearningMaterialStatus;
use App\Tests\DataLoader\LearningMaterialStatusData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadLearningMaterialStatusData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected LearningMaterialStatusData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new LearningMaterialStatus();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('learningMaterialStatus' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearningMaterialUserRole;
use App\Tests\DataLoader\LearningMaterialUserRoleData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadLearningMaterialUserRoleData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected LearningMaterialUserRoleData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new LearningMaterialUserRole();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('learningMaterialUserRoles' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

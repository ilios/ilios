<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Tests\DataLoader\ApplicationConfigData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ApplicationConfig;

final class LoadApplicationConfigData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected ApplicationConfigData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new ApplicationConfig();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setValue($arr['value']);
            $manager->persist($entity);
            $this->addReference('applicationConfigs' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

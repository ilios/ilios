<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AlertChangeType;
use App\Tests\DataLoader\AlertChangeTypeData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadAlertChangeTypeData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected AlertChangeTypeData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new AlertChangeType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);

            $manager->persist($entity);
            $this->addReference('alertChangeTypes' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

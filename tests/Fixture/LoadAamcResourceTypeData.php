<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AamcResourceType;
use App\Tests\DataLoader\AamcResourceTypeData;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

final class LoadAamcResourceTypeData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected AamcResourceTypeData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new AamcResourceType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('aamcResourceTypes' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

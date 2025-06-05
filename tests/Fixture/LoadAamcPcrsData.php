<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AamcPcrs;
use App\Tests\DataLoader\AamcPcrsData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadAamcPcrsData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected AamcPcrsData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new AamcPcrs();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $manager->persist($entity);
            $this->addReference('aamcPcrs' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

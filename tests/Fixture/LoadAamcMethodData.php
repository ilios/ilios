<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AamcMethod;
use App\Tests\DataLoader\AamcMethodData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadAamcMethodData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected AamcMethodData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new AamcMethod();
            $entity->setId($arr['id']);
            $entity->setDescription($arr['description']);
            $entity->setActive($arr['active']);

            $manager->persist($entity);
            $this->addReference('aamcMethods' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AuditLog;
use App\Tests\DataLoader\AuditLogData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadAuditLogData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected AuditLogData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new AuditLog();
            $entity->setObjectId($arr['objectId']);
            $entity->setObjectClass($arr['objectClass']);
            $entity->setValuesChanged($arr['valuesChanged']);
            $entity->setCreatedAt($arr['createdAt']);
            $entity->setAction($arr['action']);
            $manager->persist($entity);
        }
        $manager->flush();
    }
}

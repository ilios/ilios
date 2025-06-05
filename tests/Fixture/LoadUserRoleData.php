<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\UserRole;
use App\Tests\DataLoader\UserRoleData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadUserRoleData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected UserRoleData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new UserRole();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('userRoles' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

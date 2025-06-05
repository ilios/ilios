<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CourseClerkshipType;
use App\Tests\DataLoader\CourseClerkshipTypeData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCourseClerkshipTypeData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected CourseClerkshipTypeData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CourseClerkshipType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $manager->persist($entity);
            $this->addReference('courseClerkshipTypes' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AssessmentOption;
use App\Tests\DataLoader\AssessmentOptionData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadAssessmentOptionData extends AbstractFixture implements ORMFixtureInterface
{
    public function __construct(protected AssessmentOptionData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new AssessmentOption();
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);

            $manager->persist($entity);
            $this->addReference('assessmentOptions' . $arr['id'], $entity);
            $manager->flush();
        }
    }
}

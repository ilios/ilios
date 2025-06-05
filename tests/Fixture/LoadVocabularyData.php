<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\School;
use App\Entity\Vocabulary;
use App\Tests\DataLoader\VocabularyData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadVocabularyData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected VocabularyData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Vocabulary();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setActive($arr['active']);
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            $manager->persist($entity);
            $this->addReference('vocabularies' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadSchoolData::class,
        ];
    }
}

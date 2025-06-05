<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AamcResourceType;
use App\Entity\Term;
use App\Entity\Vocabulary;
use App\Tests\DataLoader\TermData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadTermData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected TermData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Term();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setDescription($arr['description']);
            $entity->setActive($arr['active']);
            $entity->setVocabulary($this->getReference('vocabularies' . $arr['vocabulary'], Vocabulary::class));
            if (isset($arr['parent'])) {
                $entity->setParent($this->getReference('terms' . $arr['parent'], Term::class));
            }
            foreach ($arr['aamcResourceTypes'] as $id) {
                $entity->addAamcResourceType($this->getReference('aamcResourceTypes' . $id, AamcResourceType::class));
            }
            $manager->persist($entity);
            $this->addReference('terms' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadVocabularyData::class,
            LoadAamcResourceTypeData::class,
        ];
    }
}

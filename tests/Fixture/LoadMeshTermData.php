<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshConcept;
use App\Entity\MeshTerm;
use App\Tests\DataLoader\MeshTermData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadMeshTermData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected MeshTermData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new MeshTerm();
            $entity->setId($arr['id']);
            $entity->setMeshTermUid($arr['meshTermUid']);
            $entity->setName($arr['name']);
            $entity->setLexicalTag($arr['lexicalTag']);
            $entity->setConceptPreferred($arr['conceptPreferred']);
            $entity->setRecordPreferred($arr['recordPreferred']);
            $entity->setPermuted($arr['permuted']);
            foreach ($arr['concepts'] as $id) {
                $entity->addConcept($this->getReference('meshConcepts' . $id, MeshConcept::class));
            }
            $this->addReference('meshTerms' . $arr['id'], $entity);
            $manager->persist($entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadMeshConceptData::class,
        ];
    }
}

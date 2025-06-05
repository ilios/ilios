<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\LearningMaterial;
use App\Entity\MeshDescriptor;
use App\Entity\Session;
use App\Entity\SessionLearningMaterial;
use App\Tests\DataLoader\SessionLearningMaterialData;
use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadSessionLearningMaterialData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected SessionLearningMaterialData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new SessionLearningMaterial();
            $entity->setId($arr['id']);
            $entity->setRequired($arr['required']);
            $entity->setPublicNotes($arr['publicNotes']);
            $entity->setNotes($arr['notes']);
            $entity->setSession($this->getReference('sessions' . $arr['session'], Session::class));
            $entity->setPosition($arr['position']);
            if (!is_null($arr['startDate'])) {
                $entity->setStartDate(new DateTime($arr['startDate']));
            }
            if (!is_null($arr['endDate'])) {
                $entity->setEndDate(new DateTime($arr['endDate']));
            }
            if ($arr['learningMaterial']) {
                $entity->setLearningMaterial(
                    $this->getReference(
                        'learningMaterials' . $arr['learningMaterial'],
                        LearningMaterial::class
                    )
                );
            }

            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            $manager->persist($entity);
            $this->addReference('sessionLearningMaterials' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadSessionData::class,
            LoadLearningMaterialData::class,
            LoadMeshDescriptorData::class,
        ];
    }
}

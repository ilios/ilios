<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\MeshDescriptor;
use App\Entity\Session;
use App\Entity\SessionObjective;
use App\Entity\Term;
use App\Tests\DataLoader\SessionObjectiveData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadSessionObjectiveData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected SessionObjectiveData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new SessionObjective();
            $entity->setId($arr['id']);
            $entity->setPosition($arr['position']);
            $entity->setActive($arr['active']);
            $entity->setTitle($arr['title']);
            $entity->setSession($this->getReference('sessions' . $arr['session'], Session::class));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id, Term::class));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor(
                    $this->getReference(
                        'sessionObjectives' . $arr['ancestor'],
                        SessionObjective::class
                    )
                );
            }
            $manager->persist($entity);

            $this->addReference('sessionObjectives' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadMeshDescriptorData::class,
            LoadTermData::class,
            LoadSessionData::class,
        ];
    }
}

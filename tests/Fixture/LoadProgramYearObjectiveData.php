<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Competency;
use App\Entity\MeshDescriptor;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearObjective;
use App\Entity\Term;
use App\Tests\DataLoader\ProgramYearObjectiveData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadProgramYearObjectiveData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected ProgramYearObjectiveData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new ProgramYearObjective();
            $entity->setId($arr['id']);
            $entity->setPosition($arr['position']);
            $entity->setActive($arr['active']);
            $entity->setTitle($arr['title']);
            $entity->setProgramYear($this->getReference('programYears' . $arr['programYear'], ProgramYear::class));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id, Term::class));
            }
            foreach ($arr['meshDescriptors'] as $id) {
                $entity->addMeshDescriptor($this->getReference('meshDescriptors' . $id, MeshDescriptor::class));
            }
            if (!empty($arr['ancestor'])) {
                $entity->setAncestor(
                    $this->getReference(
                        'programYearObjectives' . $arr['ancestor'],
                        ProgramYearObjective::class
                    )
                );
            }
            if (!empty($arr['competency'])) {
                $entity->setCompetency($this->getReference('competencies' . $arr['competency'], Competency::class));
            }
            $manager->persist($entity);

            $this->addReference('programYearObjectives' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadCompetencyData::class,
            LoadMeshDescriptorData::class,
            LoadTermData::class,
            LoadProgramYearData::class,
        ];
    }
}

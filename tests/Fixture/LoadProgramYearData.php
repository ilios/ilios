<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Competency;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\Term;
use App\Tests\DataLoader\ProgramYearData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadProgramYearData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected ProgramYearData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new ProgramYear();
            $entity->setId($arr['id']);
            $entity->setStartYear($arr['startYear']);
            $entity->setLocked($arr['locked']);
            $entity->setArchived($arr['archived']);
            $entity->setProgram($this->getReference('programs' . $arr['program'], Program::class));
            foreach ($arr['terms'] as $id) {
                $entity->addTerm($this->getReference('terms' . $id, Term::class));
            }
            foreach ($arr['competencies'] as $id) {
                $entity->addCompetency($this->getReference('competencies' . $id, Competency::class));
            }
            $manager->persist($entity);
            $this->addReference('programYears' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadProgramData::class,
            LoadTermData::class,
            LoadCompetencyData::class,
        ];
    }
}

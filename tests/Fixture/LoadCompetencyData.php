<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AamcPcrs;
use App\Entity\Competency;
use App\Entity\School;
use App\Tests\DataLoader\CompetencyData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCompetencyData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected CompetencyData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new Competency();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setActive($arr['active']);

            foreach ($arr['aamcPcrses'] as $id) {
                $entity->addAamcPcrs($this->getReference('aamcPcrs' . $id, AamcPcrs::class));
            }
            if (isset($arr['parent'])) {
                $entity->setParent($this->getReference('competencies' . $arr['parent'], Competency::class));
            }
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));

            $manager->persist($entity);
            $this->addReference('competencies' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadAamcPcrsData::class,
            LoadSchoolData::class,
        ];
    }
}

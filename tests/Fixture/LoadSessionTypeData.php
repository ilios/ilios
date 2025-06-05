<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\AamcMethod;
use App\Entity\AssessmentOption;
use App\Entity\School;
use App\Entity\SessionType;
use App\Tests\DataLoader\SessionTypeData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadSessionTypeData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected SessionTypeData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new SessionType();
            $entity->setId($arr['id']);
            $entity->setTitle($arr['title']);
            $entity->setCalendarColor($arr['calendarColor']);
            $entity->setAssessment($arr['assessment']);
            $entity->setActive($arr['active']);
            $entity->setAssessmentOption(
                $this->getReference('assessmentOptions' . $arr['assessmentOption'], AssessmentOption::class)
            );
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));

            foreach ($arr['aamcMethods'] as $id) {
                $entity->addAamcMethod($this->getReference('aamcMethods' . $id, AamcMethod::class));
            }
            $manager->persist($entity);
            $this->addReference('sessionTypes' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadAamcMethodData::class,
            LoadAssessmentOptionData::class,
            LoadSchoolData::class,
        ];
    }
}

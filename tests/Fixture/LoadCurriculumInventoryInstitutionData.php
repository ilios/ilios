<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\CurriculumInventoryInstitution;
use App\Entity\School;
use App\Tests\DataLoader\CurriculumInventoryInstitutionData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadCurriculumInventoryInstitutionData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface
{
    public function __construct(protected CurriculumInventoryInstitutionData $data)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryInstitution();
            if (!empty($arr['school'])) {
                $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            }
            $entity->setId($arr['id']);
            $entity->setName($arr['name']);
            $entity->setAamcCode($arr['aamcCode']);
            $entity->setAddressStreet($arr['addressStreet']);
            $entity->setAddressCity($arr['addressCity']);
            $entity->setAddressStateOrProvince($arr['addressStateOrProvince']);
            $entity->setAddressZipCode($arr['addressZipCode']);
            $entity->setAddressCountryCode($arr['addressCountryCode']);

            $manager->persist($entity);
            $this->addReference('curriculumInventoryInstitutions' . $arr['id'], $entity);
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

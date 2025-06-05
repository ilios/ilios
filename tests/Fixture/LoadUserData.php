<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\Cohort;
use App\Entity\CurriculumInventoryReport;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;
use App\Entity\User;
use App\Entity\UserRole;
use App\Tests\DataLoader\UserData;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class LoadUserData extends AbstractFixture implements ORMFixtureInterface, DependentFixtureInterface
{
    public function __construct(protected UserData $data)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->data->getAll();
        foreach ($data as $arr) {
            $entity = new User();
            $entity->setId($arr['id']);
            $entity->setFirstName($arr['firstName']);
            $entity->setLastName($arr['lastName']);
            $entity->setMiddleName($arr['middleName']);
            $entity->setEmail($arr['email']);
            $entity->setPreferredEmail($arr['preferredEmail']);
            $entity->setEnabled($arr['enabled']);
            $entity->setRoot($arr['root']);
            $entity->setIcsFeedKey($arr['icsFeedKey']);
            $entity->setPhone($arr['phone']);
            $entity->setCampusId($arr['campusId']);
            $entity->setUserSyncIgnore($arr['userSyncIgnore']);
            $entity->setAddedViaIlios($arr['addedViaIlios']);
            $entity->setExamined($arr['examined']);
            if (array_key_exists('otherId', $arr)) {
                $entity->setOtherId($arr['otherId']);
            }
            if (array_key_exists('displayName', $arr)) {
                $entity->setDisplayName($arr['displayName']);
            }
            if (array_key_exists('pronouns', $arr)) {
                $entity->setPronouns($arr['pronouns']);
            }
            foreach ($arr['roles'] as $id) {
                $entity->addRole($this->getReference('userRoles' . $id, UserRole::class));
            }
            foreach ($arr['cohorts'] as $id) {
                $entity->addCohort($this->getReference('cohorts' . $id, Cohort::class));
            }
            $entity->setSchool($this->getReference('schools' . $arr['school'], School::class));
            if (isset($arr['primaryCohort'])) {
                $entity->setPrimaryCohort($this->getReference('cohorts' . $arr['primaryCohort'], Cohort::class));
            }
            foreach ($arr['programYears'] as $id) {
                $entity->addProgramYear($this->getReference('programYears' . $id, ProgramYear::class));
            }
            foreach ($arr['directedSchools'] as $id) {
                $entity->addDirectedSchool($this->getReference('schools' . $id, School::class));
            }
            foreach ($arr['administeredSchools'] as $id) {
                $entity->addAdministeredSchool($this->getReference('schools' . $id, School::class));
            }
            foreach ($arr['directedPrograms'] as $id) {
                $entity->addDirectedProgram($this->getReference('programs' . $id, Program::class));
            }
            foreach ($arr['administeredCurriculumInventoryReports'] as $id) {
                $entity->addAdministeredCurriculumInventoryReport(
                    $this->getReference('curriculumInventoryReports' . $id, CurriculumInventoryReport::class)
                );
            }
            $manager->persist($entity);
            $this->addReference('users' . $arr['id'], $entity);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            LoadProgramData::class,
            LoadCohortData::class,
            LoadUserRoleData::class,
            LoadSchoolData::class,
            LoadProgramData::class,
            LoadCurriculumInventoryReportData::class,
        ];
    }
}

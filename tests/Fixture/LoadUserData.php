<?php

declare(strict_types=1);

namespace App\Tests\Fixture;

use App\Entity\User;
use App\Repository\RepositoryInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('App\Tests\DataLoader\UserData')
            ->getAll();
        /** @var RepositoryInterface $repository */
        $repository = $manager->getRepository(User::class);
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
                $entity->addRole($this->getReference('userRoles' . $id));
            }
            foreach ($arr['cohorts'] as $id) {
                $entity->addCohort($this->getReference('cohorts' . $id));
            }
            $entity->setSchool($this->getReference('schools' . $arr['school']));
            if (isset($arr['primaryCohort'])) {
                $entity->setPrimaryCohort($this->getReference('cohorts' . $arr['primaryCohort']));
            }
            foreach ($arr['programYears'] as $id) {
                $entity->addProgramYear($this->getReference('programYears' . $id));
            }
            foreach ($arr['directedSchools'] as $id) {
                $entity->addDirectedSchool($this->getReference('schools' . $id));
            }
            foreach ($arr['administeredSchools'] as $id) {
                $entity->addAdministeredSchool($this->getReference('schools' . $id));
            }
            foreach ($arr['directedPrograms'] as $id) {
                $entity->addDirectedProgram($this->getReference('programs' . $id));
            }
            foreach ($arr['administeredCurriculumInventoryReports'] as $id) {
                $entity->addAdministeredCurriculumInventoryReport(
                    $this->getReference('curriculumInventoryReports' . $id)
                );
            }
            $repository->update($entity, false, true);
            $this->addReference('users' . $arr['id'], $entity);
        }
        $repository->flush();
    }

    public function getDependencies()
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

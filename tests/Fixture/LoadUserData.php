<?php

namespace Tests\App\Fixture;

use App\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements
    ORMFixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{

    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = $this->container
            ->get('Tests\AppBundle\DataLoader\UserData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new User();
            $entity->setId($arr['id']);
            $entity->setFirstName($arr['firstName']);
            $entity->setLastName($arr['lastName']);
            $entity->setMiddleName($arr['middleName']);
            $entity->setEmail($arr['email']);
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
                $entity->addProgramYear($this->getReference('programYears'.$id));
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
            $manager->persist($entity);
            $this->addReference('users' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\AppBundle\Fixture\LoadProgramYearData',
            'Tests\AppBundle\Fixture\LoadCohortData',
            'Tests\AppBundle\Fixture\LoadUserRoleData',
            'Tests\AppBundle\Fixture\LoadSchoolData',
            'Tests\AppBundle\Fixture\LoadProgramData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryReportData',
        );
    }
}

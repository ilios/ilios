<?php

namespace Ilios\CoreBundle\Tests\Fixture;

use Ilios\CoreBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements
    FixtureInterface,
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
            ->get('ilioscore.dataloader.user')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new User();
            $entity->setId($arr['id']);
            $entity->setFirstName($arr['firstName']);
            $entity->setLastName($arr['lastName']);
            $entity->setMiddleName($arr['middleName']);
            $entity->setEmail($arr['email']);
            $entity->setEnabled($arr['enabled']);
            $entity->setIcsFeedKey($arr['icsFeedKey']);
            $entity->setPhone($arr['phone']);
            $entity->setCampusId($arr['campusId']);
            $entity->setUserSyncIgnore($arr['userSyncIgnore']);
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
            $manager->persist($entity);
            $this->addReference('users' . $arr['id'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserRoleData',
        );
    }
}

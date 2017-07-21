<?php

namespace Tests\CoreBundle\Fixture;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitution;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCurriculumInventoryInstitutionData extends AbstractFixture implements
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
            ->get('Tests\CoreBundle\DataLoader\CurriculumInventoryInstitutionData')
            ->getAll();
        foreach ($data as $arr) {
            $entity = new CurriculumInventoryInstitution();
            if (!empty($arr['school'])) {
                $entity->setSchool($this->getReference('schools' . $arr['school']));
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
            $this->addReference('curriculumInventoryInstitutions' . $arr['name'], $entity);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Tests\CoreBundle\Fixture\LoadSchoolData'
        );
    }
}

<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitution;

/**
 * Class LoadCurriculumInventoryInstitutionData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadCurriculumInventoryInstitutionData extends AbstractFixture implements DependentFixtureInterface

{
    public function __construct()
    {
        parent::__construct('curriculum_inventory_institution');
    }
    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `school_id`,`name`,`aamc_code`,`address_street`,`address_city`,`address_state_or_province`,`address_zipcode`,
        //`address_country_code`,`institution_id`
        $entity = new CurriculumInventoryInstitution();
        $entity->setSchool($this->getReference('school' . $data[0]));
        $entity->setName($data[1]);
        $entity->setAamcCode($data[2]);
        $entity->setAddressStreet($data[3]);
        $entity->setAddressCity($data[4]);
        $entity->setAddressStateOrProvince($data[5]);
        $entity->setAddressZipCode($data[6]);
        $entity->setAddressCountryCode($data[7]);
        $entity->setId($data[8]);
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }
}

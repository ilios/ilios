<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitution;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * Class LoadCurriculumInventoryInstitutionData
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
    public function getDependencies()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSchoolData',
        ];
    }

    /**
     * @return CurriculumInventoryInstitutionInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new CurriculumInventoryInstitution();
    }

    /**
     * @param CurriculumInventoryInstitutionInterface $entity
     * @param array $data
     * @return CurriculumInventoryInstitutionInterface
     *
     * AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `school_id`,`name`,`aamc_code`,`address_street`,`address_city`,
        // `address_state_or_province`,`address_zipcode`,
        // `address_country_code`,`institution_id`
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
}

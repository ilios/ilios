<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;

/**
 * Class LoadCurriculumInventoryInstitutionDataTest
 */
class LoadCurriculumInventoryInstitutionDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadCurriculumInventoryInstitutionData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadCurriculumInventoryInstitutionData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('curriculum_inventory_institution.csv');
    }

    /**
     * @param array $data
     * @param CurriculumInventoryInstitutionInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `school_id`,`name`,`aamc_code`,`address_street`,`address_city`,
        // `address_state_or_province`,`address_zipcode`,
        // `address_country_code`,`institution_id`
        $this->assertEquals($data[0], $entity->getSchool()->getId());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals($data[2], $entity->getAamcCode());
        $this->assertEquals($data[3], $entity->getAddressStreet());
        $this->assertEquals($data[4], $entity->getAddressCity());
        $this->assertEquals($data[5], $entity->getAddressStateOrProvince());
        $this->assertEquals($data[6], $entity->getAddressZipcode());
        $this->assertEquals($data[7], $entity->getAddressCountryCode());
        $this->assertEquals($data[8], $entity->getId());
    }

    /**
     * @inheritdoc
     */
    protected function getEntity(array $data)
    {
        return $this->em->findOneBy(['id' => $data[8]]);
    }
}

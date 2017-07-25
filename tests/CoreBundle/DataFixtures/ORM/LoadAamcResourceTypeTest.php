<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;

/**
 * Class LoadAamcResourceTypeDataTest
 */
class LoadAamcResourceTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\AamcResourceTypeManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcResourceTypeData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadAamcResourceTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('aamc_resource_type.csv');
    }

    /**
     * @param array $data
     * @param AamcResourceTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `resource_type_id`,`title`, `description`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getDescription());
    }
}

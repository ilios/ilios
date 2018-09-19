<?php

namespace Tests\App\DataFixtures\ORM;

use App\Entity\AamcResourceTypeInterface;
use App\Entity\TermInterface;

/**
 * Class LoadTermAamcResourceTypeTest
 */
class LoadTermAamcResourceTypeTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\TermManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadTermAamcResourceTypeData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadTermAamcResourceTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('term_x_aamc_resource_type.csv');
    }

    /**
     * @param array $data
     * @param TermInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `term_id`,`resource_type_id`
        $this->assertEquals($data[0], $entity->getId());
        // find the AAMC resource type
        $resourceId = $data[1];
        $method = $entity->getAamcResourceTypes()->filter(
            function (AamcResourceTypeInterface $resourceType) use ($resourceId) {
                return $resourceType->getId() === $resourceId;
            }
        )->first();
        $this->assertNotEmpty($method);
    }
}

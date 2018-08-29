<?php

namespace Tests\AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AamcPcrsInterface;

/**
 * Class LoadAamcPcrsDataTest
 */
class LoadAamcPcrsDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'AppBundle\Entity\Manager\AamcPcrsManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadAamcPcrsData',
        ];
    }

    /**
     * @covers \AppBundle\DataFixtures\ORM\LoadAamcPcrsData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('aamc_pcrs.csv');
    }

    /**
     * @param array $data
     * @param AamcPcrsInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `method_id`,`description`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getDescription());
    }
}

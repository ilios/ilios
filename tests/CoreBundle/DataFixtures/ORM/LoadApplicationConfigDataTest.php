<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\ApplicationConfigInterface;

/**
 * Class LoadApplicationConfigDataTest
 * @package Tests\CoreBundle\\DataFixtures\ORM
 */
class LoadApplicationConfigDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadApplicationConfigData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadApplicationConfigData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('application_config.csv');
    }

    /**
     * @param array $data
     * @param ApplicationConfigInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `name`,`value`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
        $this->assertEquals($data[2], $entity->getValue());
    }
}

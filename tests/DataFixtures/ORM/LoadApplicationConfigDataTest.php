<?php

namespace App\Tests\DataFixtures\ORM;

use App\Entity\ApplicationConfigInterface;

/**
 * Class LoadApplicationConfigDataTest
 * @package App\Tests\DataFixtures\ORM
 */
class LoadApplicationConfigDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\ApplicationConfigManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadApplicationConfigData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadApplicationConfigData::load
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

<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\AamcPcrsInterface;
use App\Repository\AamcPcrsRepository;

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
        return AamcPcrsRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadAamcPcrsData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadAamcPcrsData::load
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

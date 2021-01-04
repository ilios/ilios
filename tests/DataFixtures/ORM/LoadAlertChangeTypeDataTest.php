<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\AlertChangeTypeInterface;
use App\Repository\AlertChangeTypeRepository;

/**
 * Class LoadAlertChangeTypeDataTest
 */
class LoadAlertChangeTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return AlertChangeTypeRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadAlertChangeTypeData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadAlertChangeTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('alert_change_type.csv');
    }

    /**
     * @param array $data
     * @param AlertChangeTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `alert_change_type_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
    }
}

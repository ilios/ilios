<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

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
        return 'Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAlertChangeTypeData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadAlertChangeTypeData::load
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

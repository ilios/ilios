<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManagerInterface;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class LoadAlertChangeTypeDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadAlertChangeTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.alertchangetype.manager';
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
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadAlertChangeTypeData::load
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

<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class LoadAamcMethodDataTest
 * @package Tests\CoreBundle\\DataFixtures\ORM
 */
class LoadAamcMethodDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.aamcmethod.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcMethodData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadAamcMethodData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('aamc_method.csv');
    }

    /**
     * @param array $data
     * @param AamcMethodInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `method_id`,`description`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getDescription());
    }
}

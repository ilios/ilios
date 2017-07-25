<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\AamcMethodInterface;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class LoadSessionTypeAamcMethodDataTest
 */
class LoadSessionTypeAamcMethodDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\SessionTypeManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadSessionTypeAamcMethodData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadSessionTypeAamcMethodData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('session_type_x_aamc_method.csv');
    }

    /**
     * @param array $data
     * @param SessionTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `session_type_id`,`method_id`
        $this->assertEquals($data[0], $entity->getId());
        // find the AAMC method
        $methodId = $data[1];
        $method = $entity->getAamcMethods()->filter(function (AamcMethodInterface $method) use ($methodId) {
            return $method->getId() === $methodId;
        })->first();
        $this->assertNotEmpty($method);
    }
}

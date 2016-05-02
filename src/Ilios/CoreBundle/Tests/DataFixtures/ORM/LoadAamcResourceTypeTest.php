<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\AamcResourceTypeManagerInterface;
use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;

/**
 * Class LoadAamcResourceTypeDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadAamcResourceTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.aamcresourcetype.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAamcResourceTypeData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadAamcResourceTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('aamc_resource_type.csv');
    }

    /**
     * @param array $data
     * @param AamcResourceTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `resource_type_id`,`title`, `description`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getDescription());
    }

    /**
     * @param array $data
     * @return AamcResourceTypeInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var AamcResourceTypeManagerInterface $em
         */
        $em = $this->em;
        return $em->findAamcResourceTypeBy(['id' => $data[0]]);
    }
}

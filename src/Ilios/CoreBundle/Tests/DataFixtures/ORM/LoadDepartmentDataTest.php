<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\DepartmentManagerInterface;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class LoadDepartmentDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadDepartmentDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.department.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadDepartmentData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadDepartmentData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('department.csv');
    }

    /**
     * @param array $data
     * @param DepartmentInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `department_id`,`title`,`school_id`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
    }

    /**
     * @param array $data
     * @return DepartmentInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var DepartmentManagerInterface $em
         */
        $em = $this->em;
        return $em->findDepartmentBy(['id' => $data[0]]);
    }
}

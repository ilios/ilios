<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class LoadDepartmentDataTest
 */
class LoadDepartmentDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\DepartmentManager';
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
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadDepartmentData::load
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
}

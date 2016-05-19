<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\CourseClerkshipTypeManagerInterface;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class LoadCourseClerkshipTypeDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadCourseClerkshipTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.course_clerkship_type.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadCourseClerkshipTypeData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadCourseClerkshipTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('course_clerkship_type.csv');
    }

    /**
     * @param array $data
     * @param CourseClerkshipTypeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `course_clerkship_type_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
    }
}


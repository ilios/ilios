<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class LoadCourseClerkshipTypeDataTest
 */
class LoadCourseClerkshipTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\CourseClerkshipTypeManager';
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
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadCourseClerkshipTypeData::load
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

<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\CourseClerkshipType;

/**
 * Class LoadCourseClerkshipTypeData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadCourseClerkshipTypeData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('course_clerkship_type');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `course_clerkship_type_id`,`title`
        $entity = new CourseClerkshipType();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}

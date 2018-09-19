<?php

namespace App\DataFixtures\ORM;

use App\Entity\CourseClerkshipType;
use App\Entity\CourseClerkshipTypeInterface;
use App\Service\DataimportFileLocator;

/**
 * Class LoadCourseClerkshipTypeData
 */
class LoadCourseClerkshipTypeData extends AbstractFixture
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'course_clerkship_type');
    }

    /**
     * @return CourseClerkshipTypeInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new CourseClerkshipType();
    }

    /**
     * @param CourseClerkshipTypeInterface $entity
     * @param array $data
     * @return CourseClerkshipTypeInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `course_clerkship_type_id`,`title`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}

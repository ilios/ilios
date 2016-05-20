<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class CourseClerkshipTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseClerkshipTypeManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCourseClerkshipTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCourseClerkshipTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($courseClerkshipType, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType
    ) {
        $this->delete($courseClerkshipType);
    }

    /**
     * @deprecated
     */
    public function createCourseClerkshipType()
    {
        return $this->create();
    }
}

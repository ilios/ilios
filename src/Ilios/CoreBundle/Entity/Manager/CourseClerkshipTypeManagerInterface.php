<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Interface CourseClerkshipTypeManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CourseClerkshipTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CourseClerkshipTypeInterface
     */
    public function findCourseClerkshipTypeBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CourseClerkshipTypeInterface[]|Collection
     */
    public function findCourseClerkshipTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType,
        $andFlush = true
    );

    /**
     * @param CourseClerkshipTypeInterface $courseClerkshipType
     *
     * @return void
     */
    public function deleteCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CourseClerkshipTypeInterface
     */
    public function createCourseClerkshipType();
}

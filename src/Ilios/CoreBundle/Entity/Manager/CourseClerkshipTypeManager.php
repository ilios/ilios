<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class CourseClerkshipTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CourseClerkshipTypeManager extends BaseManager implements CourseClerkshipTypeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCourseClerkshipTypeBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCourseClerkshipTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($courseClerkshipType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($courseClerkshipType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCourseClerkshipType(
        CourseClerkshipTypeInterface $courseClerkshipType
    ) {
        $this->em->remove($courseClerkshipType);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCourseClerkshipType()
    {
        $class = $this->getClass();
        return new $class();
    }
}

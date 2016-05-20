<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SchoolManager extends DTOManager
{
    /**
     * @deprecated
     */
    public function findSchoolBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSchoolDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findDTOBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findSchoolsBy(
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
    public function findSchoolDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateSchool(
        SchoolInterface $school,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($school, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteSchool(
        SchoolInterface $school
    ) {
        $this->delete($school);
    }

    /**
     * @deprecated
     */
    public function createSchool()
    {
        return $this->create();
    }

    /**
     * @param int $schoolId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return SchoolEvent[]
     */
    public function findEventsForSchool($schoolId, \DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findEventsForSchool($schoolId, $from, $to);
    }
}

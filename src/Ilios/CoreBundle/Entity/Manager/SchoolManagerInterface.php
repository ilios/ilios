<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Interface SchoolManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SchoolManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SchoolInterface
     */
    public function findSchoolBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SchoolInterface[]
     */
    public function findSchoolsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param SchoolInterface $school
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateSchool(
        SchoolInterface $school,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param SchoolInterface $school
     *
     * @return void
     */
    public function deleteSchool(
        SchoolInterface $school
    );

    /**
     * @return SchoolInterface
     */
    public function createSchool();

    /**
     * @param int $schoolId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return SchoolEvent[]
     */
    public function findEventsForSchool($schoolId, \DateTime $from, \DateTime $to);
}

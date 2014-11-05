<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * Interface SchoolManagerInterface
 */
interface SchoolManagerInterface
{
    /** 
     *@return SchoolInterface
     */
    public function createSchool();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SchoolInterface
     */
    public function findSchoolBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return SchoolInterface[]|Collection
     */
    public function findSchoolsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param SchoolInterface $school
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSchool(SchoolInterface $school, $andFlush = true);

    /**
     * @param SchoolInterface $school
     *
     * @return void
     */
    public function deleteSchool(SchoolInterface $school);

    /**
     * @return string
     */
    public function getClass();
}

<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Interface SchoolManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface SchoolManagerInterface
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
     * @return ArrayCollection|SchoolInterface[]
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
     * @return string
     */
    public function getClass();

    /**
     * @return SchoolInterface
     */
    public function createSchool();
}

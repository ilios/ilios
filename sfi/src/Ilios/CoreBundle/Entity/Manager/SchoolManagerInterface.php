<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Interface SchoolManagerInterface
 * @package Ilios\CoreBundle\Manager
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
     * @return SchoolInterface[]|Collection
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
     *
     * @return void
     */
    public function updateSchool(
        SchoolInterface $school,
        $andFlush = true
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

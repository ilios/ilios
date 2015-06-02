<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Interface CompetencyManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CompetencyManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CompetencyInterface
     */
    public function findCompetencyBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function findCompetenciesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CompetencyInterface $competency
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateCompetency(
        CompetencyInterface $competency,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param CompetencyInterface $competency
     *
     * @return void
     */
    public function deleteCompetency(
        CompetencyInterface $competency
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CompetencyInterface
     */
    public function createCompetency();
}

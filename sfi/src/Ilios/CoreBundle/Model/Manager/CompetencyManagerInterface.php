<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CompetencyInterface;

/**
 * Interface CompetencyManagerInterface
 */
interface CompetencyManagerInterface
{
    /** 
     *@return CompetencyInterface
     */
    public function createCompetency();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CompetencyInterface
     */
    public function findCompetencyBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CompetencyInterface[]|Collection
     */
    public function findCompetenciesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CompetencyInterface $competency
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCompetency(CompetencyInterface $competency, $andFlush = true);

    /**
     * @param CompetencyInterface $competency
     *
     * @return void
     */
    public function deleteCompetency(CompetencyInterface $competency);

    /**
     * @return string
     */
    public function getClass();
}

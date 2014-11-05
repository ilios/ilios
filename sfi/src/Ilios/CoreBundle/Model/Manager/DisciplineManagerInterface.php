<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\DisciplineInterface;

/**
 * Interface DisciplineManagerInterface
 */
interface DisciplineManagerInterface
{
    /** 
     *@return DisciplineInterface
     */
    public function createDiscipline();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return DisciplineInterface
     */
    public function findDisciplineBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return DisciplineInterface[]|Collection
     */
    public function findDisciplinesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param DisciplineInterface $discipline
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateDiscipline(DisciplineInterface $discipline, $andFlush = true);

    /**
     * @param DisciplineInterface $discipline
     *
     * @return void
     */
    public function deleteDiscipline(DisciplineInterface $discipline);

    /**
     * @return string
     */
    public function getClass();
}

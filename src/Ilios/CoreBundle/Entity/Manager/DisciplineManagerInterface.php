<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Interface DisciplineManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface DisciplineManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return DisciplineInterface
     */
    public function findDisciplineBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function findDisciplinesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param DisciplineInterface $discipline
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateDiscipline(
        DisciplineInterface $discipline,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param DisciplineInterface $discipline
     *
     * @return void
     */
    public function deleteDiscipline(
        DisciplineInterface $discipline
    );

    /**
     * @return DisciplineInterface
     */
    public function createDiscipline();
}

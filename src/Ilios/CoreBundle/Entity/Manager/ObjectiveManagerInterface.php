<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Interface ObjectiveManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface ObjectiveManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ObjectiveInterface
     */
    public function findObjectiveBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ObjectiveInterface[]|Collection
     */
    public function findObjectivesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param ObjectiveInterface $objective
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateObjective(
        ObjectiveInterface $objective,
        $andFlush = true
    );

    /**
     * @param ObjectiveInterface $objective
     *
     * @return void
     */
    public function deleteObjective(
        ObjectiveInterface $objective
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return ObjectiveInterface
     */
    public function createObjective();
}

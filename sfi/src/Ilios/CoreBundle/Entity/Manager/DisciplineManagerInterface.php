<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Interface DisciplineManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface DisciplineManagerInterface
{
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
     * @param integer $limit
     * @param integer $offset
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

    /**
     * @return DisciplineInterface
     */
    public function createDiscipline();
}
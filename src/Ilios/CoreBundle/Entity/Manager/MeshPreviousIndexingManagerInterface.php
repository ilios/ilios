<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Interface MeshPreviousIndexingManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface MeshPreviousIndexingManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshPreviousIndexingInterface
     */
    public function findMeshPreviousIndexingBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshPreviousIndexingInterface[]|Collection
     */
    public function findMeshPreviousIndexingsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        $andFlush = true
    );

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     *
     * @return void
     */
    public function deleteMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return MeshPreviousIndexingInterface
     */
    public function createMeshPreviousIndexing();
}

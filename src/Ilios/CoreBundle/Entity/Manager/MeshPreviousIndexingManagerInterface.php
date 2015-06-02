<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Interface MeshPreviousIndexingManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
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
     * @return ArrayCollection|MeshPreviousIndexingInterface[]
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
     * @param bool $forceId
     *
     * @return void
     */
    public function updateMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        $andFlush = true,
        $forceId = false
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

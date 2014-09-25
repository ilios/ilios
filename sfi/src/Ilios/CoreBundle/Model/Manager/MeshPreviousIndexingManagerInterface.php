<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\MeshPreviousIndexingInterface;

/**
 * Interface MeshPreviousIndexingManagerInterface
 */
interface MeshPreviousIndexingManagerInterface
{
    /** 
     *@return MeshPreviousIndexingInterface
     */
    public function createMeshPreviousIndexing();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshPreviousIndexingInterface
     */
    public function findMeshPreviousIndexingBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshPreviousIndexingInterface[]|Collection
     */
    public function findMeshPreviousIndexingsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshPreviousIndexing(MeshPreviousIndexingInterface $meshPreviousIndexing, $andFlush = true);

    /**
     * @param MeshPreviousIndexingInterface $meshPreviousIndexing
     *
     * @return void
     */
    public function deleteMeshPreviousIndexing(MeshPreviousIndexingInterface $meshPreviousIndexing);

    /**
     * @return string
     */
    public function getClass();
}

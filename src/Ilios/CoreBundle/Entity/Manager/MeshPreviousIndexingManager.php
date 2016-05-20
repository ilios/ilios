<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Class MeshPreviousIndexingManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshPreviousIndexingManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findMeshPreviousIndexingBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findMeshPreviousIndexingsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($meshPreviousIndexing, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteMeshPreviousIndexing(
        MeshPreviousIndexingInterface $meshPreviousIndexing
    ) {
        $this->delete($meshPreviousIndexing);
    }

    /**
     * @deprecated
     */
    public function createMeshPreviousIndexing()
    {
        return $this->create();
    }
}

<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * MeshTree manager service.
 * Class MeshTreeManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTreeManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findMeshTreeBy(array $criteria, array $orderBy = null)
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findMeshTreesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateMeshTree(MeshTreeInterface $meshTree, $andFlush = true, $forceId = false)
    {
        $this->update($meshTree, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteMeshTree(MeshTreeInterface $meshTree)
    {
        $this->delete($meshTree);
    }

    /**
     * @deprecated
     */
    public function createMeshTree()
    {
        return $this->create();
    }
}

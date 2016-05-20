<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * MeshTerm manager service.
 * Class MeshTermManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTermManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findMeshTermBy(array $criteria, array $orderBy = null)
    {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findMeshTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateMeshTerm(MeshTermInterface $meshTerm, $andFlush = true, $forceId = false)
    {
        $this->update($meshTerm, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteMeshTerm(MeshTermInterface $meshTerm)
    {
        $this->delete($meshTerm);
    }

    /**
     * @deprecated
     */
    public function createMeshTerm()
    {
        return $this->create();
    }
}

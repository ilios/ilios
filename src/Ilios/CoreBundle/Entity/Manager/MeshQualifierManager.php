<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class MeshQualifierManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshQualifierManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findMeshQualifierBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findMeshQualifiersBy(
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
    public function updateMeshQualifier(
        MeshQualifierInterface $meshQualifier,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($meshQualifier, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteMeshQualifier(
        MeshQualifierInterface $meshQualifier
    ) {
        $this->delete($meshQualifier);
    }

    /**
     * @deprecated
     */
    public function createMeshQualifier()
    {
        return $this->create();
    }
}
